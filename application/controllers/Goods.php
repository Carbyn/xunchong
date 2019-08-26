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

}
