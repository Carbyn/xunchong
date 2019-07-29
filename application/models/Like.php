<?php
class LikeModel extends AbstractModel {

    const TABLE = '`like`';

    public function like($user_id, $goods_id) {
        $create_time = time();
        $data = compact('user_id', 'goods_id', 'create_time');
        $id = $this->db->table(self::TABLE)->insert($data);
        return $id;
    }

    public function dislike($user_id, $goods_id) {
        $where = compact('user_id', 'goods_id');
        return $this->db->table(self::TABLE)->where($where)->delete();
    }

    public function liked($user_id, $goods_id) {
        $where = compact('user_id', 'goods_id');
        $row = $this->db->table(self::TABLE)->where($where)->get();
        return $row ? 1 : 0;
    }

    public function multiLiked($user_id, $goods_ids) {
        $where = compact('user_id');
        $data = $this->db->table(self::TABLE)->where($where)
            ->in('goods_id', $goods_ids)->getAll();
        $ret = [];
        foreach($data as $row) {
            $ret[$row->goods_id] = 1;
        }
        return $ret;
    }

    public function likeNum($goods_id) {
        $where = compact('goods_id');
        $count = $this->db->table(self::TABLE)->where($where)->count('id', 'count')->get();
        return (int)$count->count;
    }

    public function multiLikeNum($goods_ids) {
        $data = $this->db->table(self::TABLE)->select('goods_id')
            ->count('id', 'count')
            ->in('goods_id', $goods_ids)
            ->groupBy('goods_id')
            ->getAll();
        $ret = [];
        foreach($data as $row) {
            $ret[$row->goods_id] = (int)$row->count;
        }
        return $ret;
    }

}
