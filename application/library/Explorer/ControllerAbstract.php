<?php
namespace Explorer;
class ControllerAbstract extends \Yaf\Controller_Abstract {

    public $userId;

    public function init() {
        $loginModel = new \LoginModel();
        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : '';
        if ($token) {
            $this->userId = $loginModel->verifyToken($token);
        }
    }

    public function outputError($status, $msg, $data = []) {
        $data = compact('status', 'msg', 'data');
        return $this->outputJson($data);
    }

    public function outputSuccess($data = []) {
        $data = [
            'status' => 0,
            'msg' => 'succ',
            'data' => $data,
        ];
        return $this->outputJson($data);
    }

    public function outputJson($data) {
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
    }

}
