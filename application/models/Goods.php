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

	public function existsByGoodsID($id) {
		$where['id'] = $id;
		$goods = $this->db->table(self::TABLE)->where($where)->get();
		return !empty($goods);
	}

    public function fetch($id, $uid = 0) {
        $where['id'] = $id;
        $goods = $this->db->table(self::TABLE)->where($where)->get();
        $goods = $this->format($goods);
        $goods['liked'] = 0;
        if ($uid) {
            $likeModel = new LikeModel();
            $goods['liked'] = $likeModel->liked($uid, $id);
        }
        return $goods;
    }

    public function batchFetch($ids) {
        $goods_list = $this->db->table(self::TABLE)
            ->in('id', $ids)
            ->getAll();
        if (empty($goods_list)) {
            return [];
        }
        foreach($goods_list as &$goods) {
            $goods = $this->format($goods);
        }
        return $goods_list;
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

    public function fetchAll($level, $cid, $query, $pn, $ps, $uid) {
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
            $sql = 'select * from '.self::TABLE." where match(title) against(? IN BOOLEAN MODE)";
            if (!empty($where)) {
                $whereStr = key($where).'='.current($where);
                $sql .= ' and '.$whereStr;
            }
            $sql .= " and status = 0 order by score desc limit $offset, $ps";
            $goods_list = $this->db->query($sql, [$query]);
        } else {
            $goods_list = $this->db->table(self::TABLE);
            $where['status'] = 0;
            $goods_list = $goods_list->where($where);
            $goods_list = $goods_list->orderBy('score', 'DESC')->limit($offset, $ps)->getAll();
        }

        if (empty($goods_list)) {
            return [];
        }
        $gids = [];
        foreach($goods_list as &$goods) {
            $goods = $this->format($goods);
            $gids[] = $goods['id'];
        }

        if ($uid ) {
            $likeModel = new LikeModel();
            $liked = $likeModel->multiLiked($uid, $gids);
            foreach($goods_list as &$goods) {
                if (isset($liked[$goods['id']])) {
                    $goods['liked'] = 1;
                } else {
                    $goods['liked'] = 0;
                }
            }
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
        $goods['reserve_price'] = round($goods['reserve_price'], 2);
        $goods['final_price'] = round($goods['final_price'], 2);
        if ($goods['platform'] == Constants::GOODS_PLATFORM_JDK) {
            $goods['reserve_price'] = $goods['final_price'];
        }

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

        $goods['lowest_type'] = [];
        $goods['lowest_price'] = $goods['final_price'];
        $goods['lowest_num'] = 1;

        if ($this->hasPromoZeng($goods)) {
            $goods['lowest_type'][] = '赠';
        }

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

        sort($goods['lowest_type']);

        if ($goods['click_url_tpwd']) {
            $goods['click_url_tpwd'] = "{$goods['title']}\n💰原价{$goods['reserve_price']}，💰优惠后{$goods['lowest_price']}\n{$goods['click_url_tpwd']} 打开淘宝立即抢购~";
        }
        if ($goods['coupon_click_url_tpwd']) {
            $goods['coupon_click_url_tpwd'] = "{$goods['title']}\n💰原价{$goods['reserve_price']}，💰优惠后{$goods['lowest_price']}\n{$goods['coupon_click_url_tpwd']} 打开淘宝立即抢购~";
        }

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
        $price_types = [
            Constants::PROMO_FENSIJIA => '粉丝价',
            Constants::PROMO_PLUSJIA => 'Plus价',
            Constants::PROMO_MIAOSHAJIA => '秒杀价',
            Constants::PROMO_CUXIAOJIA => '促销价',
        ];
        foreach($price_types as $type => $text) {
            if (isset($coupon_info[$type]) && $this->inPeriod($coupon_info[$type])) {
                if ($coupon_info[$type]['ext']['price'] < $promo_price) {
                    $promo_price_type = $text;
                    $promo_price = $coupon_info[$type]['ext']['price'];
                }
            }
        }

        if (!$promo_price_type) {
            return [];
        }
        $promo_price = round($promo_price, 2);

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
        if ($zhekou['discount'] > 0  && $manjian['discount'] > 0) {
            if ($zhekou['discount'] > $manjian['discount']) {
                $total_money -= $zhekou['discount'];
                $lowest_type[] = $zhekou['text'];
            } else {
                $total_money -= $manjian['discount'];
                $lowest_type[] = $manjian['text'];
            }
        } else if ($zhekou['discount'] > 0) {
            $total_money -= $zhekou['discount'];
            $lowest_type[] = $zhekou['text'];
        } else if ($manjian['discount'] > 0) {
            $total_money -= $manjian['discount'];
            $lowest_type[] = $manjian['text'];
        }
        $coupon = $this->hasCoupon($price, $promos, $num);
        if ($coupon['discount'] > 0) {
            $total_money -= $coupon['discount'];
            $lowest_type[] = $coupon['text'];
        }

        $lowest_price = round($total_money/$num, 2);

        return compact('lowest_price', 'lowest_type');
    }

    private function hasZhekou($price, $promos, $num) {
        $discount = 0;
        $text = '';
        if (isset($promos[Constants::PROMO_ZHEKOU])) {
            foreach($promos[Constants::PROMO_ZHEKOU] as $p) {
                if (!$this->inPeriod($p)) {
                    continue;
                }
                foreach($p['ext'] as $e) {
                    if ($num != $e['needNum']) {
                        continue;
                    }
                    $tmp_max = $e['needNum']*$price*(1-$e['rebate']/10);
                    $discount = max($discount, $tmp_max);
                    $text = sprintf('%d件%s折', $e['needNum'], $e['rebate']);
                }
            }
        }
        return compact('discount', 'text');
    }

    private function hasManjian($price, $promos, $num) {
        $discount = 0;
        $text = '';
        if (isset($promos[Constants::PROMO_MANJIAN])) {
            foreach($promos[Constants::PROMO_MANJIAN] as $p) {
                if (!$this->inPeriod($p)) {
                    continue;
                }
                foreach($p['ext'] as $e) {
                    if ($price*$num < $e['needMoney']) {
                        continue;
                    }
                    $discount = max($discount, $e['rewardMoney']);
                    $text = sprintf('满%d减%s', $e['needMoney'], $e['rewardMoney']);
                }
            }
        }
        return compact('discount', 'text');
    }

    private function hasCoupon($price, $promos, $num) {
        $discount = 0;
        $text = '';
        if (isset($promos[Constants::PROMO_COUPON])) {
            foreach($promos[Constants::PROMO_COUPON] as $p) {
                if (!$this->inPeriod($p)) {
                    continue;
                }
                foreach($p['ext'] as $e) {
                    if ($price*$num < $e['needMoney']) {
                        continue;
                    }
                    $discount = max($discount, $e['rewardMoney']);
                    $text = sprintf('%d减%s券', $e['needMoney'], $e['rewardMoney']);
                }
            }
        }
        return compact('discount', 'text');
    }

    private function inPeriod($promo) {
        $time = time();
        if ($promo['starttime'] && $promo['endtime']) {
            return $promo['starttime'] < $time && $time < $promo['endtime'];
        }
        return true;
    }

}
