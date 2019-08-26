<?php
class GoodsController extends \Explorer\ControllerAbstract {

    const VER_IN_REVIEW = '0.3';

    public function listAction() {
        $level = (int)$this->getRequest()->getQuery('level', 0);
        $cid = (int)$this->getRequest()->getQuery('cid', 0);
        $bid = (int)$this->getRequest()->getQuery('bid', 0);
        $query = mb_substr($this->getRequest()->getQuery('query', ''), 0, 100);
        $pn = (int)$this->getRequest()->getQuery('pn', 1);
        $coupon = (int)$this->getRequest()->getQuery('coupon', 0);
        $ps = 10;
        $ver = $this->getRequest()->getQuery('ver');
        $in_review = $ver == self::VER_IN_REVIEW;

        $goodsModel = new GoodsModel();

        $goods_list = [];
        if ($oid = $this->isJD($query)) {
            $goods = $goodsModel->exists($oid, \Constants::GOODS_PLATFORM_JDK);
            if ($goods && $goods['coupon_click_url'] != '') {
                $goods_list = [$goods];
            }
        } else if ($oid = $this->isTB($query)) {
            $goods = $goodsModel->exists($oid, \Constants::GOODS_PLATFORM_TBK);
            if ($goods && $goods['coupon_click_url'] != '') {
                $goods_list = [$goods];
            }
        } else {
            if (!$level && !$cid && !$query) {
                $goods_list_cats = $goodsModel->fetchAll(1, 100, $bid, $query, $pn, $ps / 2, $this->userId, $in_review, $coupon);
                $goods_list_dogs = $goodsModel->fetchAll(1, 200, $bid, $query, $pn, $ps / 2, $this->userId, $in_review, $coupon);
                $goods_list = array_merge($goods_list_cats, $goods_list_dogs);
                shuffle($goods_list);
            } else {
                $goods_list = $goodsModel->fetchAll($level, $cid, $bid, $query, $pn, $ps, $this->userId, $in_review, $coupon);
            }

            if ($pn == 1 && $query) {
                $this->afterQuery($query, $cid, count($goods_list));
            }
        }

        $is_end = count($goods_list) < $ps;
        $this->outputSuccess(compact('goods_list', 'is_end'));
    }

    public function detailAction() {
        $id = $this->getRequest()->getQuery('id');
        if (!$id) {
            return $this->outputError(Constants::ERR_GOODS_PARAM_INVALID, '商品不存在');
        }
        $goodsModel = new GoodsModel();
        $goods = $goodsModel->fetch($id, $this->userId);
        if (!$goods) {
            return $this->outputError(Constants::ERR_GOODS_PARAM_INVALID, '商品不存在');
        }
        $this->outputSuccess(compact('goods'));
    }

    private function afterQuery($query, $cid, $count) {
        $is_lacked = (int)($count < \Constants::GOODS_QUERY_MIN);
        $queryModel = new QueryModel();
        if ($queryModel->exists($query, $cid)) {
            $queryModel->update($query, $cid, $is_lacked);
        } else {
            $queryModel->create($query, $cid, $is_lacked, 1);
        }
    }

    private function isJD($query) {
        if (preg_match('#https://item.m.jd.com/product/(\d+).html#', $query, $matches)) {
            return $matches[1];
        }
        return 0;
    }

    private function isTB($query) {
        if (preg_match('#https://m.tb.cn/[a-zA-Z0-9\.\?=]*#', $query, $matches)) {
            $url = $matches[0];
            $html = \Explorer\Fetcher::getWithRetry($url);
            if (!$html) {
                return 0;
            }
            if (preg_match('#&id=(\d+)#', $html, $matches)) {
                return $matches[1];
            }
        }
        return 0;
    }

}
