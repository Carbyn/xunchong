<?php
class LikeController extends \Explorer\ControllerAbstract {

    public function doAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $goods_id = $this->getRequest()->getQuery('goods_id');
        $type = (int)$this->getRequest()->getQuery('like', 1);
        // TODO
        // whether goods exists
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

}
