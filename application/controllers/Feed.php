<?php
class FeedController extends \Explorer\ControllerAbstract {

    public function publishAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '���ȵ�¼');
        }
        $mobile = $this->getRequest()->getPost('mobile');
        if (!\Explorer\Validation::isMobileValid($mobile)) {
            return $this->outputError(Constants::ERR_FEED_MOBILE_INVALID, '�ֻ�����Ч');
        }
        $type = $this->getRequest()->getPost('type');
        $event_time = $this->getRequest()->getPost('event_time');
        $event_address = $this->getRequest()->getPost('event_address');
        $reward = $this->getRequest()->getPost('reward');
        $text = $this->getRequest()->getPost('text');
        $articleModel = new ArticleModel();
        $id = $articleModel->publish($this->userId, $mobile, $type, $event_time, $event_address, $reward, $text);
        $this->outputSuccess(compact('id'));
    }

    public function addImageAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '���ȵ�¼');
        }
        $id = $this->getRequest()->getPost('id');
        $articleModel = new ArticleModel();
        $article = $articleModel->fetch($id);
        if (!$article) {
            return $this->outputError(Constants::ERR_FEED_ARTICLE_NOT_EXISTS, '���²�����');
        }
        if ($article->author->id != $this->userId) {
            return $this->outputError(Constants::ERR_FEED_UNAUTHORIZED, 'û��Ȩ��');
        }

	    $upload_path = APPLICATION_PATH.'/uploads';
	    if (!@file_exists($upload_path)) {
	        mkdir($upload_path);
	    }
        $name = 'image';
	    $files = $this->getRequest()->getFiles();
	    if (empty($files[$name])) {
            return $this->outputError(Constants::ERR_FEED_NO_IMAGE, '���ϴ�ͼƬ');
	    }
        $file = $files[$name];
        $tmp = explode('.', $file['name']);
        $ext = '.'.$tmp[count($tmp) - 1];
        $img_name = uniqid(true).$ext;
        if ($file['error'] == 0 && !empty($file['name'])) {
            move_uploaded_file($file['tmp_name'], $upload_path.'/'.$img_name);
        } else {
            return $this->outputError(Constants::ERR_FEED_UPLOAD_FAILED, '������');
        }
        $image = 'https://xunchong.1024.pm/uploads/'.$img_name;
        $articleModel->addImage($id, $image);
        $this->outputSuccess();
    }

    public function deleteAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '���ȵ�¼');
        }
        $id = $this->getRequest()->getQuery('id');
        $articleModel = new ArticleModel();
        $articleModel->delete($id);
        $this->outputSuccess();
    }

    public function feedAction() {
        $page = (int)$this->getRequest()->getQuery('page', 1);
        $type = (int)$this->getRequest()->getQuery('type', 0);
        $articleModel = new ArticleModel();
        if ($type == ArticleModel::TYPE_MINE) {
            if (!$this->userId) {
                return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '���ȵ�¼');
            }
            $feed = $articleModel->feed($page, 0, $this->userId);
        } else {
            $feed = $articleModel->feed($page, $type);
        }
        $this->outputSuccess(compact('feed'));
    }

}

