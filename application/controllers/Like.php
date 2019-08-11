<?php
class LikeController extends \Explorer\ControllerAbstract {

    public function doAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $goods_id = $this->getRequest()->getQuery('goods_id');
        $type = (int)$this->getRequest()->getQuery('like', 1);

        $goodsModel = new GoodsModel();
        if (!$goodsModel->fetch($goods_id)) {
            return $this->outputError(Constants::ERR_GOODS_PARAM_INVALID, '商品不存在');
        }

        $likeModel = new LikeModel();
        if ($type == 1) {
            if ($likeModel->liked($this->userId, $goods_id)) {
                return $this->outputSuccess();
            }
            $likeModel->like($this->userId, $goods_id);
        } else {
            $likeModel->dislike($this->userId, $goods_id);
        }
        $this->outputSuccess();
    }

    public function listAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }

        $pn = (int)$this->getRequest()->getQuery('pn', 1);
        $ps = 10;

        $likeModel = new LikeModel();
        $goods_list = $likeModel->fetchLiked($this->userId, $pn, $ps);
        $is_end = count($goods_list) < $ps;

        $this->outputSuccess(compact('goods_list', 'is_end'));
    }

}
