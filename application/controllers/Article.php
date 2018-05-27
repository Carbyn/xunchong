<?php
class ArticleController extends \Explorer\ControllerAbstract {

    public function postAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $mobile = $this->getRequest()->getPost('mobile');
        $type = (int)$this->getRequest()->getPost('type');
        $event_time = $this->getRequest()->getPost('event_time', '');
        $event_address = $this->getRequest()->getPost('event_address', '');
        $reward = (int)$this->getRequest()->getPost('reward', 0);
        $text = $this->getRequest()->getPost('text', '');
        $articleModel = new ArticleModel();
        if (!\Explorer\Validation::isMobileValid($mobile)) {
            return $this->outputError(Constants::ERR_ARTICLE_MOBILE_INVALID, '手机号无效');
        }
        if (!$articleModel->isTypeValid($type)) {
            return $this->outputError(Constants::ERR_ARTICLE_TYPE_INVALID, '类型无效');
        }
        if ($type == ArticleModel::TYPE_XUNCHONG || $type == ArticleModel::TYPE_YOUYUAN) {
            if (!$event_time) {
                return $this->outputError(Constants::ERR_ARTICLE_EVENTTIME_INVALID, '时间不能为空');
            }
            if (!$event_address) {
                return $this->outputError(Constants::ERR_ARTICLE_EVENTADDRESS_INVALID, '位置不能为空');
            }
        }
        if (!$text) {
            return $this->outputError(Constants::ERR_ARTICLE_TEXT_INVALID, '说点什么吧');
        }
        $id = $articleModel->publish($this->userId, $mobile, $type, $event_time, $event_address, $reward, $text);
        $this->outputSuccess(compact('id'));
    }

    public function addImageAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $id = $this->getRequest()->getPost('id', 0);
        $articleModel = new ArticleModel();
        $article = $articleModel->fetch($id, $this->userId);
        if (!$article) {
            return $this->outputError(Constants::ERR_ARTICLE_ARTICLE_NOT_EXISTS, '文章不存在');
        }
        if ($article->author->id != $this->userId) {
            return $this->outputError(Constants::ERR_ARTICLE_UNAUTHORIZED, '没有权限');
        }

	    $upload_path = APPLICATION_PATH.'/uploads';
	    if (!@file_exists($upload_path)) {
	        mkdir($upload_path);
	    }
        $name = 'image';
	    $files = $this->getRequest()->getFiles();
	    if (empty($files[$name])) {
            return $this->outputError(Constants::ERR_ARTICLE_NO_IMAGE, '请上传图片');
	    }
        $file = $files[$name];
        $tmp = explode('.', $file['name']);
        $ext = '.'.$tmp[count($tmp) - 1];
        $img_name = uniqid(true).$ext;
        if ($file['error'] == 0 && !empty($file['name'])) {
            move_uploaded_file($file['tmp_name'], $upload_path.'/'.$img_name);
        } else {
            return $this->outputError(Constants::ERR_ARTICLE_UPLOAD_FAILED, '请重试');
        }
        $image = 'https://xunchong.1024.pm/uploads/'.$img_name;
        $articleModel->addImage($id, $image);
        $this->outputSuccess();
    }

    public function detailAction() {
        $id = $this->getRequest()->getQuery('id', 0);
        $articleModel = new ArticleModel();
        $article = $articleModel->fetch($id, $this->userId);
        if (!$article) {
            return $this->outputError(Constants::ERR_ARTICLE_ARTICLE_NOT_EXISTS, '文章不存在');
        }
        if ($this->userId && $this->userId == $article->author->id) {
            $article->isAuthor = 1;
        } else {
            $article->isAuthor = 0;
        }
        $this->outputSuccess(compact('article'));
    }

    public function deleteAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $id = $this->getRequest()->getQuery('id');
        $articleModel = new ArticleModel();
        if ($articleModel->isAuthor($id, $this->userId)) {
            $articleModel->delete($id);
        }
        $this->outputSuccess();
    }

    public function closeAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $id = $this->getRequest()->getQuery('id');
        $articleModel = new ArticleModel();
        if ($articleModel->isAuthor($id, $this->userId)) {
            $articleModel->close($id);
        }
        $this->outputSuccess();
    }

}
