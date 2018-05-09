<?php
class ArticleModel extends AbstractModel {

    const TYPE_XUNCHONG = 1;
    const TYPE_XUNZHU   = 2;
    const TYPE_LINGYANG = 3;

    const TABLE = 'article';

    public function publish($author, $type, $event_time, $content) {
        $data = compact('author', 'type', 'event_time', 'content');
        $data['pub_time'] = time();
        $id = $this->db->table(self::TABLE)->insert($data);
        return $id;
    }

    public function delete($id) {
        $where = ['id' => $id];
        $this->db->table(self::TABLE)->delete($where);
    }

    public function fetch($id) {
        $article = $this->db->table(self::TABLE)->where($where);
        if (empty($article)) {
            return [];
        }
        $userModel = new UserModel();
        $author = $userModel->fetch($article->author);
        $article->author = $author;
        return $article;
    }

    public function feed($page = 1, $type = 0) {
        $where = [];
        if ($this->isTypeValid($type)) {
            $where = ['type' => $type];
        }
        $page = max(1, $page);
        $limit = ($page - 1) * 10;
        $offset = 10;
        $feed = $this->db->table(self::TABLE);
        if (!empty($where)) {
            $feed = $feed->where($where);
        }
        $feed = $feed->orderBy('pub_time', 'desc')
            ->limit($limit, $offset)
            ->getAll();

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
                $ret[] = $article;
            }
        }
        return $ret;
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
