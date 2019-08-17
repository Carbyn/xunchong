<?php
class Cat2brandModel extends AbstractModel {

    const TABLE = 'cat2brand';

    public function create($data) {
        return $this->db->table(self::TABLE)->insert($data);
    }

    public function fetchByCidAndBid($cid, $bid) {
        $where = compact('cid', 'bid');
        return $this->db->table(self::TABLE)->where($where)->get();
    }

    public function fetchAll() {
        return (array)$this->db->table(self::TABLE)->getAll();
    }

}
