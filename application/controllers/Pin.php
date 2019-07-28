<?php
class PinController extends \Explorer\ControllerAbstract {

    public function pinAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $goods_id = (int)$this->getRequest()->getQuery('goods_id');

        // TODO
        // 1. fetch goods info
        // 2. fetch latest goods screenshot
        // 3. if goods info has no updates, then use latest screenshot id, otherwise create a new screenshot
        // 4. pin

        $screenshot_id = $goods_id;
        $pinModel = new PinModel();
        if ($pinModel->hasPinned($this->userId, $screenshot_id)) {
            return $this->outputSuccess();
        }
        if (!$pinModel->pin($this->userId, $screenshot_id)) {
            return $this->outputError(Constants::ERR_SYS_ERROR, '服务异常，请稍后重试');
        }
        $this->outputSuccess();
    }

    public function unpinAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $screenshot_id = (int)$this->getRequest()->getQuery('screenshot_id');

        $pinModel = new PinModel();
        $pinModel->unpin($this->userId, $screenshot_id);
        $this->outputSuccess();
    }

}
