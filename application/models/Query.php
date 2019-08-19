<?php
class QueryModel extends AbstractModel {

    const TABLE = 'query';

    public function create($query, $cid, $is_lacked, $times) {
        $data = compact('query', 'cid', 'is_lacked', 'times');
        return $this->db->table(self::TABLE)->insert($data);
    }

    public function exists($query, $cid) {
        $where = compact('query', 'cid');
        return $this->db->table(self::TABLE)->where($where)->get();
    }

    public function update($query, $cid, $is_lacked) {
        $sql = 'update '.self::TABLE.' set is_lacked=?, times=times+1 where query=? and cid=?';
        $this->db->query($sql, [$is_lacked, $query, $cid]);
    }

    public function fetchAll() {
    }

}
