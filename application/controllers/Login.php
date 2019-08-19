<?php
class LoginController extends \Explorer\ControllerAbstract {

    /*
    public function sendCodeAction() {
        $mobile = $this->getRequest()->getQuery('mobile');
        if (!\Explorer\Validation::isMobileValid($mobile)) {
            return $this->outputError(Constants::ERR_LOGIN_MOBILE_INVALID, '手机号无效');
        }
        $code = \Explorer\Utils::generateCode(4);
        // TODO
        $code = '1111';
        if (!\Explorer\Sms::sendCode($mobile, $code)) {
            return $this->outputError(Constants::ERR_LOGIN_SEND_FAILED, '发送验证码失败，请稍后重试');
        }
        $loginModel = new LoginModel();
        $loginModel->saveCode($mobile, $code);
        $this->outputSuccess();
    }

    public function loginAction() {
        $mobile = $this->getRequest()->getQuery('mobile');
        $code = $this->getRequest()->getQuery('code');
        $loginModel = new LoginModel();
        if (!$loginModel->verifyCode($mobile, $code)) {
            return $this->outputError(Constants::ERR_LOGIN_WRONG_CODE, '验证码错误');
        }
        $userModel = new UserModel();
        $user = $userModel->exists($mobile);
        if (!$user) {
            $id = $userModel->create($mobile);
            $user = $userModel->fetch($id);
        }
        $token = \Explorer\Utils::generateToken(32);
        $loginModel->saveToken($id, $token);
        $this->outputSuccess(compact('token', 'user'));
    }
     */

    public function wxloginAction() {
        $code = $this->getRequest()->getQuery('code');
        if (empty($code)) {
            return $this->outputError(Constants::ERR_LOGIN_CODE_INVALID, 'code无效');
        }
        $openid = \Explorer\Weixin::jscode2session($code);
        if (!$openid) {
            return $this->outputError(Constants::ERR_LOGIN_CODE_INVALID, 'code无效');
        }
        $userModel = new UserModel();
        $user = $userModel->existsOpenid($openid);
        if (!$user) {
            $id = $userModel->createOpenid($openid);
            $user = $userModel->fetch($id);
        }
        $token = \Explorer\Utils::generateToken(32);
        $loginModel = new LoginModel();
        $loginModel->saveToken($user->id, $token);
        $this->outputSuccess(compact('token', 'user'));
    }

    public function verifyTokenAction() {
        $token = $this->getRequest()->getQuery('token');
        $loginModel = new LoginModel();
        if (!($id = $loginModel->verifyToken($token))) {
            return $this->outputError(Constants::ERR_LOGIN_WRONG_TOKEN, 'token无效');
        }
        $userModel = new UserModel();
        $user = $userModel->fetch($id);
        if (!$user) {
            return $this->outputError(Constants::ERR_LOGIN_WRONG_TOKEN, 'token无效');
        }
        $this->outputSuccess(compact('user'));
    }

}

