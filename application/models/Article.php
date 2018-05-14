<?php
class ArticleModel extends AbstractModel {

    const TYPE_XUNCHONG = 1;
    const TYPE_XUNZHU   = 2;
    const TYPE_LINGYANG = 3;
    const TYPE_MINE     = 100;

    const TABLE = 'article';

    public function publish($author, $mobile, $type, $event_time, $event_address, $reward, $text) {
        $data = compact('author', 'mobile', 'type', 'event_time', 'event_address', 'reward', 'text');
        $data['pub_time'] = time();
        $id = $this->db->table(self::TABLE)->insert($data);
        return $id;
    }

    public function addImage($id, $image) {
        $where['id'] = $id;
        $article = $this->db->table(self::TABLE)->where($where)->get();
        if ($article->images) {
            $images = $article->images.'|'.$image;
        } else {
            $images = $image;
        }
        $update = compact('images');
        $this->db->table(self::TABLE)->where($where)->update($update);
        return true;
    }

    public function close($id) {
        $where['id'] = $id;
        $update['closed'] = 1;
        $this->db->table(self::TABLE)->where($where)->update($update);
    }

    public function delete($id) {
        $where['id'] = $id;
        $this->db->table(self::TABLE)->delete($where);
    }

    public function fetch($id) {
        $where['id'] = $id;
        $article = $this->db->table(self::TABLE)->where($where)->get();
        if (empty($article)) {
            return [];
        }
        $article = $this->images2arr($article);
        $userModel = new UserModel();
        $author = $userModel->fetch($article->author);
        $article->author = $author;
        return $article;
    }

    public function isAuthor($id, $userId) {
        $article = $this->fetch($id);
        if ($article && $article->author->id == (int)$userId) {
            return true;
        }
        return false;
    }

    public function feed($page = 1, $pagesize = 10, $type = 0, $author = 0) {
        $where = [];
        if ($this->isTypeValid($type)) {
            $where['type'] = $type;
        }
        if ($author) {
            $where['author'] = $author;
        }
        $page = max(1, $page);
        $pagesize = max(10, min(15, $page));
        $limit = ($page - 1) * $pagesize;
        $feed = $this->db->table(self::TABLE);
        if (!empty($where)) {
            $feed = $feed->where($where);
        }
        $feed = $feed->orderBy('pub_time', 'desc')
            ->limit($limit, $pagesize)
            ->getAll();
        if (empty($feed)) {
            return [];
        }

        $authors = [];
        foreach($feed as $article) {
            $authors[] = $article->author;
        }

        $userModel = new UserModel();
        $authors = $userModel->fetchAll($authors);

        $ret = [];
        foreach($feed as $article) {
            if (isset($authors[$article->author])) {
                $article->author = $authors[$article->author];
                $article = $this->images2arr($article);
                $article->isAuthor = $type == self::TYPE_MINE ? 1 : 0;
                $ret[] = $article;
            }
        }
        return $ret;
    }

    private function images2arr($article) {
        if ($article->images) {
            $article->images = explode('|', $article->images);
        } else {
            $article->images = [];
        }
        return $article;
    }

    public function isTypeValid($type) {
        if (in_array($type, [
            self::TYPE_XUNCHONG,
            self::TYPE_XUNZHU,
            self::TYPE_LINGYANG,
            ])) {
            return true;
        }
        return false;
    }

}
