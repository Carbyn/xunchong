<?php
class ReportModel extends AbstractModel {

    const TABLE = 'report';

    public function create($goods_id, $content) {
        $update_time = time();
        $data = compact('goods_id', 'content', 'update_time');
        return $this->db->table(self::TABLE)->insert($data);
    }

    public function getByGoodsId($goods_id) {
        $where['goods_id'] = $goods_id;
        return $this->db->table(self::TABLE)->where($where)->get();
    }

    public function update($goods_id, $content) {
        $update_time = time();
        $update = compact('content', 'update_time');
        $where['goods_id'] = $goods_id;
        return $this->db->table(self::TABLE)->where($where)->update($update);
    }
}
