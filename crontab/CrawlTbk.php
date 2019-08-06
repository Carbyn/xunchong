<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlTbk', 'run']);

class CrawlTbk {

    public static function run() {
        $favList = \Explorer\Tbk::getFavoritesList();
        if (!$favList) {
            echo "getFavoritesList failed\n";
            return;
        }
        foreach($favList->results->tbk_favorites as $fav) {
            for($i = 1; $i < 10; $i++) {
                $items = \Explorer\Tbk::getFavoritesItem($fav->favorites_id, $i, 100);
                if (!$items) {
                    echo "getFavoritesItem:$fid failed\n";
                    continue;
                }
                foreach($items->results->uatm_tbk_item as &$item) {
                    $item = (array)$item;
                    $urls = ['click_url', 'coupon_click_url'];
                    foreach($urls as $url) {
                        $tpwd = \Explorer\Tbk::createTpwd($item[$url], $item['title'], $item['pict_url']);
                        if (!$tpwd) {
                            echo "createTpwd:{$item['num_iid']} failed\n";
                            continue;
                        }
                        $item[$url.'_tpwd'] = $tpwd->data->model;
                    }
                    var_dump($item);exit;
                }
            }
        }
    }

}
