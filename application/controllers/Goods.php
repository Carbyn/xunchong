<?php
class GoodsController extends \Explorer\ControllerAbstract {

    public function listAction() {
        $pn = $this->getRequest()->getQuery('pn', 1);
        $ps = 10;
        $goodsModel = new GoodsModel();
        $goods_list = $goodsModel->fetchAll($pn, $ps);
        $this->outputSuccess(compact('goods_list'));
    }

}
