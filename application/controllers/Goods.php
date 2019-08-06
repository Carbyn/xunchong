<?php
class GoodsController extends \Explorer\ControllerAbstract {

    public function listAction() {
        $pn = $this->getRequest()->getQuery('pn', 1);
        $ps = 10;
        $goodsModel = new GoodsModel();
        $goods_list = $goodsModel->fetchAll($pn, $ps);
        $this->outputSuccess(compact('goods_list'));
    }

    public function detailAction() {
        $id = $this->getRequest()->getQuery('id');
        if (!$id) {
            return $this->outputError(Constants::ERR_GOODS_ID_INVALID, '商品不存在');
        }
        $goodsModel = new GoodsModel();
        $goods = $goodsModel->fetch($id);
        if (!$goods) {
            return $this->outputError(Constants::ERR_GOODS_ID_INVALID, '商品不存在');
        }
        $this->outputSuccess(compact('goods'));
    }

}
