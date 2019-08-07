<?php
class ReportController extends \Explorer\ControllerAbstract {

    public function submitAction() {
        $goodsId = (string)$this->getRequest()->getQuery('goods_id', '');
        $itemId  = (string)$this->getRequest()->getQuery('item_id', '');
        list($errno, $errmsg) = $this->reqValidation($goodsId, $itemId);
        if (0 !== $errno) {
            return $this->outputError($errno, $errmsg);
        }

        $reportModel = new ReportModel();
        $reports = $reportModel->getByGoodsId($goodsId);
        if (empty($reports)) {
            $content = json_encode(array($itemId=>1));
            $ret = $reportModel->create($goodsId, $content);
        } else {
            $content = json_decode($reports->content, true);
            if (isset($content[$itemId])) {
                $content[$itemId] = $content[$itemId] + 1;
            } else {
                $content[$itemId] = 1;
            }
            $content = json_encode($content);
            $ret = $reportModel->update($goodsId, $content);
        }
        if (empty($ret)) {
            return $this->outputError(Constants::ERR_USER_DATA_EMPTY, '操作失败');
        }
        return $this->outputSuccess();
    }

    private function reqValidation($goodsId, $itemId) {
        $errno = Constants::ERR_SYS_PARAM_INVALID;
        if (!$goodsId) {
            return array($errno, 'goods_id不能为空');
        }
        if (!$itemId || !$this->isItemidValid($itemId)) {
            return array($errno, 'item_id为空or不合法');
        }
        $goodsModel = new GoodsModel();
        if (!$goodsModel->exists($goodsId, $platform=1)) {
            return array($errno, 'goods not exists');
        }
        return array(0, '');
    }

    private function isItemidValid($itemId) {
        $items = [
            '001' => '虚假信息',
            '002' => '失效信息',
        ];
        return isset($items[$itemId]);
    }
}
