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
            ->orderBy('score', 'DESC')
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

    public function fetchAll($level, $cid, $query, $pn, $ps) {
        $pn = max($pn, 1);
        $offset = ($pn - 1) * $ps;

        $where = [];
        if ($level && $cid) {
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
            }
        }

        if ($query) {
            $sql = 'select * from '.self::TABLE." where match(title) against(?)";
            if (!empty($where)) {
                $whereStr = key($where).'='.current($where);
                $sql .= ' and '.$whereStr;
            }
            $sql .= " order by score desc limit $offset, $ps";
            $goods_list = $this->db->query($sql, [$query]);
        } else {
            $goods_list = $this->db->table(self::TABLE);
            if (!empty($where)) {
                $goods_list = $goods_list->where($where);
            }
            $goods_list = $goods_list->orderBy('score', 'DESC')->limit($offset, $ps)->getAll();
        }

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

        if (!empty($goods['union_coupon_info'])) {
            $goods['union_coupon_info'] = json_decode($goods['union_coupon_info'], true);
        } else {
            $goods['union_coupon_info'] = [];
        }

        if (!empty($goods['official_coupon_info'])) {
            $goods['official_coupon_info'] = json_decode($goods['official_coupon_info'], true);
        } else {
            $goods['official_coupon_info'] = [];
        }

        $goods[Constants::PROMO_ZENG] = $this->hasPromoZeng($goods);

        $goods['lowest_type'] = [];
        $goods['lowest_price'] = $goods['final_price'];
        $goods['lowest_num'] = 1;

        $promo_prices = $this->hasPromoPrice($goods);
        if ($promo_prices) {
            $goods['lowest_type'][] = $promo_prices['promo_price_type'];
            $goods['lowest_price'] = $promo_prices['promo_price'];
        }

        // try num: 1, 2, 3 to get a lowest price and promos plan
        $lowest_price = $goods['lowest_price'];
        $lowest_type = [];
        $lowest_num = 1;
        for ($i = 1; $i < 4; $i++) {
            $promo_discount = $this->hasDiscount($goods, $i);
            if (!$promo_discount) {
                continue;
            }
            if ($promo_discount['lowest_price'] < $lowest_price) {
                $lowest_price = $promo_discount['lowest_price'];
                $lowest_type = $promo_discount['lowest_type'];
                $lowest_num = $i;
            }
        }

        $goods['lowest_price'] = $lowest_price;
        $goods['lowest_type'] = array_merge($goods['lowest_type'], $lowest_type);
        $goods['lowest_num'] = $lowest_num;

        return $goods;
    }

    // const PROMO_ZENG     = 'zeng';
    private function hasPromoZeng($goods) {
        if (empty($goods['official_coupon_info'])) {
            return 0;
        }
        $time = time();
        if (!empty($goods['official_coupon_info'][Constants::PROMO_ZENG])
            && $this->inPeriod($goods['official_coupon_info'][Constants::PROMO_ZENG])) {
            return 1;
        }
        return 0;
    }

    // const PROMO_FENSIJIA = 'fensijia';
    // const PROMO_PLUSJIA  = 'plusjia';
    // const PROMO_MIAOSHAJIA = 'miaoshajia';
    // const PROMO_CUXIAOJIA = 'cuxiaojia';
    private function hasPromoPrice($goods) {
        $coupon_info = $goods['official_coupon_info'];
        if (empty($coupon_info)) {
            return [];
        }

        $promo_price_type = '';
        $promo_price = $goods['final_price'];

        $time = time();
        $price_types = [Constants::PROMO_FENSIJIA, Constants::PROMO_PLUSJIA, Constants::PROMO_MIAOSHAJIA, Constants::PROMO_CUXIAOJIA];
        foreach($price_types as $type) {
            if (isset($coupon_info[$type]) && $this->inPeriod($coupon_info[$type])) {
                if ($coupon_info[$type]['ext']['price'] < $promo_price) {
                    $promo_price_type = $type;
                    $promo_price = $coupon_info[$type]['ext']['price'];
                }
            }
        }

        if (!$promo_price_type) {
            return [];
        }
        $promo_price = sprintf('%.2f', $promo_price);

        return compact('promo_price_type', 'promo_price');
    }

    // const PROMO_MANJIAN = 'manjian';
    // const PROMO_ZHEKOU  = 'zhekou';
    // const PROMO_COUPON   = 'coupon';// union & official
    // (zhekou|manjian)&coupon
    private function hasDiscount($goods, $num) {
        $price = $goods['lowest_price'];

        // union
        $promos = [];
        if ($goods['official_coupon_info']) {
            $promos = array_merge($promos, $goods['official_coupon_info']);
        }
        if ($goods['union_coupon_info']) {
            $promos[Constants::PROMO_COUPON][] = $goods['union_coupon_info'];
        }
        if (empty($promos)) {
            return [];
        }

        $total_money = $price * $num;
        $lowest_type = [];

        $zhekou = $this->hasZhekou($price, $promos, $num);
        $manjian = $this->hasManjian($price, $promos, $num);
        if ($zhekou && $manjian) {
            if ($zhekou > $manjian) {
                $total_money -= $zhekou;
                $lowest_type[] = Constants::PROMO_ZHEKOU;
            } else {
                $total_money -= $manjian;
                $lowest_type[] = Constants::PROMO_MANJIAN;
            }
        } else if ($zhekou) {
            $total_money -= $zhekou;
            $lowest_type[] = Constants::PROMO_ZHEKOU;
        } else if ($manjian) {
            $total_money -= $manjian;
            $lowest_type[] = Constants::PROMO_MANJIAN;
        }
        $coupon = $this->hasCoupon($price, $promos, $num);
        if ($coupon) {
            $total_money -= $coupon;
            $lowest_type[] = Constants::PROMO_COUPON;
        }

        $lowest_price = sprintf('%.2f', $total_money/$num);

        return compact('lowest_price', 'lowest_type');
    }

    private function hasZhekou($price, $promos, $num) {
        if (!isset($promos[Constants::PROMO_ZHEKOU])) {
            return 0;
        }
        $max = 0;
        foreach($promos[Constants::PROMO_ZHEKOU] as $p) {
            if (!$this->inPeriod($p)) {
                continue;
            }
            foreach($p['ext'] as $e) {
                if ($num != $e['needNum']) {
                    continue;
                }
                $tmp_max = $e['needNum']*$price*(1-$e['rebate']/10);
                $max = max($max, $tmp_max);
            }
        }
        return $max;
    }

    private function hasManjian($price, $promos, $num) {
        if (!isset($promos[Constants::PROMO_MANJIAN])) {
            return 0;
        }

        $max = 0;
        foreach($promos[Constants::PROMO_MANJIAN] as $p) {
            if (!$this->inPeriod($p)) {
                continue;
            }
            foreach($p['ext'] as $e) {
                if ($price*$num < $e['needMoney']) {
                    continue;
                }
                $max = max($max, $e['rewardMoney']);
            }
        }
        return $max;
    }

    private function hasCoupon($price, $promos, $num) {
        if (!isset($promos[Constants::PROMO_COUPON])) {
            return 0;
        }
        $max = 0;
        foreach($promos[Constants::PROMO_COUPON] as $p) {
            if (!$this->inPeriod($p)) {
                continue;
            }
            foreach($p['ext'] as $e) {
                if ($price*$num < $e['needMoney']) {
                    continue;
                }
                $max = max($max, $e['rewardMoney']);
            }
        }
        return $max;
    }

    private function inPeriod($promo) {
        $time = time();
        if ($promo['starttime'] && $promo['endtime']) {
            return $promo['starttime'] < $time && $time < $promo['endtime'];
        }
        return true;
    }

}
