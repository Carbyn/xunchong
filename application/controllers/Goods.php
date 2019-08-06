<?php
class GoodsController extends \Explorer\ControllerAbstract {

    public function listAction() {
        $pn = $this->getRequest()->getQuery('pn', 1);
        $ps = 10;
        $goodsModel = new GoodsModel();
        $goods_list = $goodsModel->fetchAll($pn, $ps);
        $is_end = count($goods_list) < $ps;
        $this->outputSuccess(compact('goods_list', 'is_end'));
    }

    public function searchAction() {
        $query = $this->getRequest()->getQuery('query');
        $pn = $this->getRequest()->getQuery('pn', 1);
        $ps = 10;
        if (!$query) {
            return $this->outputError(Constants::ERR_GOODS_PARAM_INVALID, '请输入搜索内容');
        }
        $goodsModel = new GoodsModel();
        $goods_list = $goodsModel->search($query, $pn, $ps);
        $is_end = count($goods_list) < $ps;
        $this->outputSuccess(compact('goods_list', 'is_end'));
    }

    public function detailAction() {
        $id = $this->getRequest()->getQuery('id');
        if (!$id) {
            return $this->outputError(Constants::ERR_GOODS_PARAM_INVALID, '商品不存在');
        }
        $goodsModel = new GoodsModel();
        $goods = $goodsModel->fetch($id);
        if (!$goods) {
            return $this->outputError(Constants::ERR_GOODS_PARAM_INVALID, '商品不存在');
        }
        $this->outputSuccess(compact('goods'));
    }

}
