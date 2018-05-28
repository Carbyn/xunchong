<?php
class ArticleModel extends AbstractModel {

    const TYPE_DEFAULT  = 1;
    const TYPE_XUNCHONG = 2;
    const TYPE_YOUYUAN  = 3;
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
        $this->db->table(self::TABLE)->where($where)->delete();
    }

    public function fetch($id, $userId) {
        $where['id'] = $id;
        $article = $this->db->table(self::TABLE)->where($where)->get();
        if (empty($article)) {
            return [];
        }
        $article = $this->images2arr($article);
        $userModel = new UserModel();
        $author = $userModel->fetch($article->author);
        $article->author = $author;
        $likeModel = new LikeModel();
        $article->liked = $likeModel->liked($userId, $id);
        $article->likeNum = $likeModel->likeNum($id);
        return $article;
    }

    public function isAuthor($id, $userId) {
        $article = $this->fetch($id);
        if ($article && $article->author->id == (int)$userId) {
            return true;
        }
        return false;
    }

    public function fetchMine($page, $pagesize, $author) {
        $where['author'] = $author;
        return $this->feed($page, $pagesize, $where, $author);
    }

    public function fetchAll($page, $pagesize, $type, $userId) {
        $where = [];
        if ($this->isTypeValid($type)) {
            $where['type'] = $type;
        }
        return $this->feed($page, $pagesize, $where, $userId);
    }

    public function feed($page, $pagesize, $where, $userId) {
        $page = max(1, $page);
        $pagesize = max(10, min(15, $pagesize));
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
        $article_ids = [];
        foreach($feed as $article) {
            $authors[] = $article->author;
            $article_ids[] = $article->id;
        }

        $userModel = new UserModel();
        $authors = $userModel->fetchAll($authors);

        $likeModel = new LikeModel();
        $likeNums = $likeModel->multiLikeNum($article_ids);
        if ($userId) {
            $liked = $likeModel->multiLiked($userId, $article_ids);
        }

        $ret = [];
        foreach($feed as $article) {
            if (isset($authors[$article->author])) {
                $article->author = $authors[$article->author];
                $article = $this->images2arr($article);
                $article->isAuthor = $article->author->id == $userId ? 1 : 0;
                $article->liked = ($userId && isset($liked[$article->id])) ? 1 : 0;
                $article->likeNum = isset($likeNums[$article->id]) ? $likeNums[$article->id] : 0;
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
            self::TYPE_DEFAULT,
            self::TYPE_XUNCHONG,
            self::TYPE_YOUYUAN,
            ])) {
            return true;
        }
        return false;
    }

}
