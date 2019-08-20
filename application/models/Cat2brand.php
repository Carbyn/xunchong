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

    public function fetchBrandsByCid($cid) {
        $where['cid'] = $cid;
        $rows = $this->db->table(self::TABLE)->where($where)->getAll();
        if (empty($rows)) {
            return [];
        }
        $bids = [];
        foreach($rows as $row) {
            $bids[] = $row->bid;
        }

        $brandModel = new BrandModel();
        $brands = $brandModel->batchFetch($bids);
        return $brands;
    }

    public function fetchAll() {
        return (array)$this->db->table(self::TABLE)->getAll();
    }

}
