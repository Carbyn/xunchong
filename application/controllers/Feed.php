<?php
class FeedController extends \Explorer\ControllerAbstract {

    public function feedAction() {
        $page = (int)$this->getRequest()->getQuery('page', 1);
        $type = (int)$this->getRequest()->getQuery('type', 0);
        $pagesize = 10;
        $articleModel = new ArticleModel();
        if ($type == ArticleModel::TYPE_MINE) {
            if (!$this->userId) {
                return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
            }
            $feed = $articleModel->fetchMine($page, $pagesize, $this->userId);
        } else {
            $feed = $articleModel->fetchAll($page, $pagesize, $type, $this->userId);
        }
        $isEnd = 0;
        if (count($feed) < $pagesize) {
            $isEnd = 1;
        }
        $this->outputSuccess(compact('feed', 'isEnd'));
    }

}
