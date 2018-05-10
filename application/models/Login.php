<?php
class LoginModel extends AbstractModel {

    const TTL_CODE  = 600;
    const TTL_TOKEN = 86400 * 365;

    public function saveCode($mobile, $code) {
        $redis = new Predis\Client();
        $key = $this->getCodeKey($mobile);
        $redis->set($key, $code);
        $redis->expire($key, self::TTL_CODE);
        return true;
    }

    public function verifyCode($mobile, $code) {
        $redis = new Predis\Client();
        $key = $this->getCodeKey($mobile);
        $storeCode = $redis->get($key);
        $ret = strval($code) === $storeCode;
        if ($ret) {
            $redis->del($key);
        }
        return $ret;
    }

    public function saveToken($id, $token) {
        $redis = new Predis\Client();
        $key = $this->getTokenKey($token);
        $redis->set($key, $id);
        $redis->expire($key, self::TTL_TOKEN);
        return true;
    }

    public function verifyToken($token) {
        $redis = new Predis\Client();
        $key = $this->getTokenKey($token);
        $id = $redis->get($key);
        return $id;
    }

    private function getCodeKey($mobile) {
        return md5('login_code_'.$mobile);
    }

    private function getTokenKey($id) {
        return md5('login_token_'.$id);
    }

}
