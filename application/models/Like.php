<?php
class LikeModel extends AbstractModel {

    const TABLE = '`like`';

    public function like($author_id, $article_id) {
        $data = compact('author_id', 'article_id');
        $data['pub_time'] = time();
        $id = $this->db->table(self::TABLE)->insert($data);
        return $id;
    }

    public function dislike($author_id, $article_id) {
        $where = compact('author_id', 'article_id');
        $this->db->table(self::TABLE)->where($where)->delete();
    }

    public function liked($author_id, $article_id) {
        $where = compact('author_id', 'article_id');
        $row = $this->db->table(self::TABLE)->where($where)->get();
        return $row ? 1 : 0;
    }

    public function multiLiked($author_id, $article_ids) {
        $where = compact('author_id');
        $data = $this->db->table(self::TABLE)->where($where)
            ->in('article_id', $article_ids)->getAll();
        $ret = [];
        foreach($data as $row) {
            $ret[$row->article_id] = 1;
        }
        return $ret;
    }

    public function likeNum($article_id) {
        $where = compact('article_id');
        $count = $this->db->table(self::TABLE)->where($where)->count('id', 'count');
        return $count->count;
    }

    public function multiLikeNum($article_ids) {
        $article_ids = implode(',', $article_ids);
        $sql = 'select article_id, count(id) as count from '
            .self::TABLE.' where article_id in (?) group by article_id';
        $data = $this->db->query($sql, [$article_ids]);
        $ret = [];
        foreach($data as $row) {
            $ret[$row->article_id] = $row->count;
        }
        return $ret;
    }

}
