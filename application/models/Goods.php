<?php
class GoodsModel extends AbstractModel {

    const TABLE = 'goods';

    public function create($data) {
        $id = $this->db->table(self::TABLE)->insert($data);
        return $id;
    }

    public function exists($oid, $platform) {
        $where = compact('oid', 'platform');
        $goods = $this->db->table(self::TABLE)->where($where)->get();
        return $this->format($goods);
    }

    public function fetch($id) {
        $where['id'] = $id;
        $goods = $this->db->table(self::TABLE)->where($where)->get();
        return $this->format($goods);
    }

    public function fetchAllByPlatform($platform, $pn, $ps) {
        $where['platform'] = $platform;
        $offset = ($pn - 1) * $ps;
        $goods_list = $this->db->table(self::TABLE)
            ->where($where)
            ->orderBy('id', 'DESC')
            ->limit($offset, $ps)
            ->getAll();
        if (empty($goods_list)) {
            return [];
        }
        foreach($goods_list as &$goods) {
            $goods = $this->format($goods);
        }
        return $goods_list;
    }

    public function fetchAll($pn, $ps) {
        $offset = ($pn - 1) * $ps;
        $goods_list = $this->db->table(self::TABLE)
            ->orderBy('id', 'DESC')
            ->limit($offset, $ps)
            ->getAll();
        if (empty($goods_list)) {
            return [];
        }
        foreach($goods_list as &$goods) {
            $goods = $this->format($goods);
        }
        return $goods_list;
    }

    public function fetchByCid($level, $cid, $pn, $ps) {
        switch($level) {
        case 1:
            $where['cat_id'] = $cid;
            break;
        case 2:
            $where['s_cat_id'] = $cid;
            break;
        case 3:
            $where['leaf_cat_id'] = $cid;
            break;
        default:
            return [];
        }
        $offset = ($pn - 1) * $ps;
        $goods_list = $this->db->table(self::TABLE)
            ->where($where)
            ->orderBy('id', 'DESC')
            ->limit($offset, $ps)
            ->getAll();
        if (empty($goods_list)) {
            return [];
        }
        foreach($goods_list as &$goods) {
            $goods = $this->format($goods);
        }
        return $goods_list;
    }

    public function search($query, $pn, $ps) {
        $pn = (int)$pn;
        $offset = ($pn - 1) * $ps;
        $sql = 'select * from '.self::TABLE." where match(title) against(?) order by id desc limit $offset, $ps";
        $goods_list = $this->db->query($sql, [$query]);
        if (empty($goods_list)) {
            return [];
        }
        foreach($goods_list as &$goods) {
            $goods = $this->format($goods);
        }
        return $goods_list;
    }

    public function update($id, $update) {
        $where['id'] = $id;
        return $this->db->table(self::TABLE)->where($where)->update($update);
    }

    private function format($goods) {
        if (empty($goods)) {
            return false;
        }
        $goods = (array)$goods;
        $goods['small_images'] = empty($goods['small_images']) ? [] : explode('|', $goods['small_images']);

        // union
        if ($goods['union_coupon_info']) {
            $coupon_info = json_decode($goods['union_coupon_info'], true);
            $time = time();
            if ($coupon_info['starttime'] < $time && $time < $coupon_info['endtime']) {
                $min = $goods['final_price'];
                foreach($coupon_info['ext'] as $e) {
                    $num = ceil($e['needMoney'] / $goods['final_price']);
                    $tmp_min = $num * $goods['final_price'] - $e['rewardMoney'];
                    $min = $tmp_min < $min ? $tmp_min : $min;
                }
                $goods['lowest_price'] = sprintf('%.2f', $min);
                $goods['lowest_num'] = $num;
            }
        }

        return $goods;
    }

}
