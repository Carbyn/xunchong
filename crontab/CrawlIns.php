<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlIns', 'run']);

class CrawlIns {

    const INS_URL_CAT = 'https://www.instagram.com/graphql/query/?query_hash=ded47faa9a1aaded10161a2ff32abb6b&variables=%s';
    const FETCH_IMG_CMD = 'wget -t 5 -T 5 %s -O %s';
    const PAGES_PER_TIME = 30;

    public static function run() {
        $end_cursor = '';
        $i = 0;
        while (true) {
            if ($i++ >= self::PAGES_PER_TIME) {
                echo "get enough nodes\n";
                break;
            }
            $vars = self::buildVariables($end_cursor);
            $url = sprintf(self::INS_URL_CAT, $vars);
            echo "fetch: $url\n";
            $resp = self::fetchUrl($url);
            if (!$resp) {
                echo "resp empty\n";
                break;
            }
            $edges = $resp['data']['hashtag']['edge_hashtag_to_media']['edges'];
            if (empty($edges)) {
                echo "edges empty\n";
                break;
            }
            foreach($edges as $edge) {
                if ($edge['node']['is_video']) {
                    continue;
                }
                if (self::exists($edge['node']['id'])) {
                    echo $edge['node']['id']." exists\n";
                    continue;
                }
                self::save($edge['node']);
            }
            $pagInfo = $resp['data']['hashtag']['edge_hashtag_to_media']['page_info'];
            if (!$pageInfo['has_next_page']) {
                echo "has_next_page false\n";
                break;
            }
            $end_cursor = $pageInfo['end_cursor'];
            echo "end_cursor: $end_cursor\n";
        }
    }

    private static function buildVariables($after = '') {
        if ($after == '') {
            $after = 'AQCQJVWnWWqYKyRMFaWY7ZGCn5IkzhAEO-f_RlETUUMVG-YkNVBSXfDQy1qXjCJ5cUaAh2lDhe1k3fNMfjE6RWiFdMwcl_EN-okniM6VKoQbyA';
        }
        $tag_name = '猫チョコピーカンで猫助け';
        $first = 8;
        return urlencode(json_encode(compact('tag_name', 'first', 'after')));
    }

    private static function fetchUrl($url) {
        $curl = new \Curl\Curl();

        $resp = '';
        $retry = 3;
        while ($retry-- > 0) {
            $curl->get($url);
            if ($curl->error) {
                return false;
            } else {
                $resp = $curl->response;
                break;
            }
        }
        if (!$resp) {
            return false;
        }
        $resp = @json_decode($resp, true);
        if ($resp['status'] != 'ok') {
            return false;
        }
        return $resp;
    }

    private static function exists($sid) {
        $articleModel = new ArticleModel();
        return $articleModel->sidExists($sid);
    }

    private static function save($node) {
        $articleModel = new ArticleModel();
        $author = self::getAuthor();
        $mobile = '';
        $type = ArticleModel::TYPE_DEFAULT;
        $event_time = '';
        $event_address = '';
        $reward = 0;
        $text = '';
        $pub_time = $node['taken_at_timestamp'];
        $sid = $node['id'];
        if (!empty($node['edge_media_to_caption']['edges'][0]['node']['text'])) {
            // TODO
            // $text = $node['edge_media_to_caption']['edges'][0]['node']['text'];
        }
        $id = $articleModel->publish($author, $mobile, $type, $event_time, $event_address, $reward, $text, $pub_time);
        if (!$id) {
            echo "save node failed\n";
            return false;
        }
        $image = self::fetchImg($node['thumbnail_src']);
        if (!$image) {
            echo "fetchImg failed\n";
            $articleModel->delete($id);
            return false;
        }
        if (!$articleModel->addImage($id, $image)) {
            echo "save addImage failed\n";
            return false;
        }
        return true;
    }

    private static function getAuthor() {
        static $authors = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,
        ];
        return $authors[array_rand($authors)];
    }

    private static function fetchImg($img) {
	    $upload_path = APPLICATION_PATH.'/uploads';
	    if (!@file_exists($upload_path)) {
	        mkdir($upload_path);
	    }
        $tmp = explode('.', $img);
        $ext = '.'.$tmp[count($tmp) - 1];
        $img_name = uniqid(true).$ext;
        $img_path = $upload_path.'/'.$img_name;
        $cmd = sprintf(self::FETCH_IMG_CMD, $img, $img_path);
        echo $cmd."\n";
        @exec($cmd, $out, $status);
        if ($status != 0) {
            echo "fetchImg failed\n";
            return false;
        }
        $image = 'https://xunchong.1024.pm/uploads/'.$img_name;
        return $image;
    }

}
