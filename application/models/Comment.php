<?php
class CommentModel extends AbstractModel {

    const TABLE = 'comment';

    public function post($article_id, $author_id, $author_name, $text,
        $reply_author_id = 0, $reply_author_name = '') {

        $data = compact('article_id', 'author_id', 'author_name',
            'text', 'reply_author_id', 'reply_author_name');
        $data['pub_time'] = time();
        $id = $this->db->table(self::TABLE)->insert($data);
        return $id;
    }

    public function fetch($id) {
        $where['id'] = $id;
        return $this->db->table(self::TABLE)->where($where)->get();
    }

    public function delete($id) {
        $where['id'] = $id;
        $this->db->table(self::TABLE)->where($where)->delete();
    }

    public function more($article_id, $page = 1, $pagesize = 10, $userId = 0) {
        $where['article_id'] = $article_id;
        $page = max(1, $page);
        $pagesize = max(10, min(15, $pagesize));
        $limit = ($page - 1) * $pagesize;
        $comments = $this->db->table(self::TABLE)
            ->where($where)
            ->orderBy('pub_time', 'asc')
            ->limit($limit, $pagesize)
            ->getAll();
        foreach($comments as &$comment) {
            $comment['isAuthor'] = $comment['author_id'] == $userId ? 1 : 0;
        }
        return $comments;
    }

}
