<?php
class CategoryModel extends AbstractModel {

    const TABLE = 'category';

    public function create($data) {
        return $this->db->table(self::TABLE)->insert($data);
    }

    public function update($id, $update) {
        $where['id'] = $id;
        return $this->db->table(self::TABLE)->where($where)->update($update);
    }

    public function fetch($cid) {
        $where['cid'] = $cid;
        return $this->db->table(self::TABLE)->where($where)->get();
    }

    public function fetchAll() {
        $categories = $this->db->table(self::TABLE)->getAll();
        if (empty($categories)) {
            return false;
        }
        $data = [];
        foreach($categories as $c) {
            $c = (array)$c;
            if ($c['pcid'] == 0) {
                $data[] = $c;
            }
        }
        foreach($data as &$c) {
            foreach($categories as $cc) {
                $cc = (array)$cc;
                if ($cc['pcid'] == $c['cid']) {
                    $c['children'][] = $cc;
                }
            }
        }
        foreach($data as &$c) {
            foreach($c['children'] as &$cc) {
                foreach($categories as $ccc) {
                    $ccc = (array)$ccc;
                    if ($ccc['pcid'] == $cc['cid']) {
                        $cc['children'][] = $ccc;
                    }
                }
            }
        }
        return $data;
    }

    public function fetchCatByName($name) {
        return 101;
    }

    public function fetchLeafCatByName($name) {
        return 10101;
    }

}
