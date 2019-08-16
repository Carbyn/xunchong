<?php
class GoodsController extends \Explorer\ControllerAbstract {

    public function listAction() {
        $level = $this->getRequest()->getQuery('level', 0);
        $cid = $this->getRequest()->getQuery('cid', 0);
        $query = $this->getRequest()->getQuery('query', '');
        $pn = (int)$this->getRequest()->getQuery('pn', 1);
        $ps = 10;

        if ($level && $cid) {
            $categoryModel = new CategoryModel();
            if (!$categoryModel->fetch($cid)) {
                return $this->outputError(Constants::ERR_GOODS_PARAM_INVALID, '类目不存在');
            }
        }

        $goodsModel = new GoodsModel();
        if (!$level && !$cid && !$query) {
            $goods_list_cats = $goodsModel->fetchAll(1, 100, $query, $pn, $ps / 2, $this->userId);
            $goods_list_dogs = $goodsModel->fetchAll(1, 200, $query, $pn, $ps / 2, $this->userId);
            $goods_list = array_merge($goods_list_cats, $goods_list_dogs);
            shuffle($goods_list);
        } else {
            $goods_list = $goodsModel->fetchAll($level, $cid, $query, $pn, $ps, $this->userId);
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

}
