<?php
class BrandModel extends AbstractModel {

    const TABLE = 'brand';

    public function create($data) {
        return $this->db->table(self::TABLE)->insert($data);
    }

    public function fetchByName($name) {
        $where = compact('name');
        return $this->db->table(self::TABLE)->where($where)->get();
    }

    public function batchFetch($bids) {
        $brands = $this->db->table(self::TABLE)->in('id', $bids)
            ->orderBy('id', 'ASC')->getAll();
        if (empty($brands)) {
            return [];
        }
        foreach($brands as &$b) {
            $b = (array)$b;
        }
        return $brands;
    }

    public function fetchAll() {
        $brands = $this->db->table(self::TABLE)->getAll();
        if (empty($brands)) {
            return [];
        }
        foreach($brands as &$b) {
            $b = (array)$b;
        }
        return (array)$brands;
    }

}
