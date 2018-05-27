<?php
class LikeController extends \Explorer\ControllerAbstract {

    public function doAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $article_id = $this->getRequest()->getQuery('article_id');
        $type = (int)$this->getRequest()->getQuery('like', 1);
        $articleModel = new ArticleModel();
        if (!$articleModel->fetch($article_id)) {
            return $this->outputError(Constants::ERR_LIKE_ARTICLE_NOT_EXISTS, '文章不存在');
        }
        $likeModel = new LikeModel();
        if ($type == 1) {
            $likeModel->like($this->userId, $article_id);
        } else {
            $likeModel->dislike($this->userId, $article_id);
        }
        $this->outputSuccess();
    }

}
