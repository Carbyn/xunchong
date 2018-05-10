<?php
class UserController extends \Explorer\ControllerAbstract {

    public function updateProfileAction() {
        if (!$this->userId) {
            return $this->outputError(Constants::ERR_SYS_NOT_LOGGED, '请先登录');
        }
        $name = $this->getRequest()->getPost('name');
        $avatar = $this->getRequest()->getPost('avatar');
        $address = $this->getRequest()->getPost('address');
        $data = [];
        if ($name) {
            $data['name'] = $name;
        }
        if ($avatar) {
            $data['avatar'] = $avatar;
        }
        if ($address) {
            $data['address'] = $address;
        }
        if (empty($data)) {
            return $this->outputError(Constants::ERR_USER_DATA_EMPTY, '更新内容为空');
        }
        $userModel = new UserModel();
        $userModel->updateProfile($this->userId, $data);
        $this->outputSuccess();
    }

}

