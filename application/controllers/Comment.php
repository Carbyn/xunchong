<?php
class CommentController extends \Explorer\ControllerAbstract {

    public function postAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $article_id = $this->getRequest()->getPost('article_id', 0);
        $text = $this->getRequest()->getPost('text', '');
        $reply_author_id = $this->getRequest()->getPost('reply_author_id', 0);
        $reply_author_name = $this->getRequest()->getPost('reply_author_name', '');
        $articleModel = new ArticleModel();
        $article = $articleModel->fetch($article_id);
        if (!$article) {
            return $this->outputError(Constants::ERR_COMMENT_ARTICLE_NOT_EXISTS, '文章不存在');
        }
        if (!$text) {
            return $this->outputError(Constants::ERR_COMMENT_TEXT_INVALID, '内容不能为空');
        }
        $userModel = new UserModel();
        if ($reply_author_id) {
            $user = $userModel->fetch($reply_author_id);
            if (!$user) {
                return $this->outputError(Constants::ERR_COMMENT_REPLY_AUTHOR_ID_INVALID, '回复人不存在');
            }
            $reply_author_name = $user->name;
        }
        $commentModel = new CommentModel();
        $comment_id = $commentModel->post($article_id, $this->userId, $this->user->name, $text, $reply_author_id, $reply_author_name);
        $comment = $commentModel->fetch($comment_id);
        $this->outputSuccess(compact('comment'));
    }

    public function deleteAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $id = $this->getRequest()->getQuery('id', 0);
        $commentModel = new CommentModel();
        $comment = $commentModel->fetch($id);
        if ($comment->author_id == $this->userId) {
            $comment->delete($id);
        }
        $this->outputSuccess();
    }

    public function moreAction() {
        $article_id = $this->getRequest()->getQuery('article_id', 0);
        $page = (int)$this->getRequest()->getQuery('page', 1);
        $pagesize = 10;
        $commentModel = new CommentModel();
        $comments = $commentModel->more($article_id, $page, $size, $this->userId);
        $isEnd = 0;
        if (count($comments) < $pagesize) {
            $isEnd = 1;
        }
        $this->outputSuccess(compact('comments', 'isEnd'));
    }
}
