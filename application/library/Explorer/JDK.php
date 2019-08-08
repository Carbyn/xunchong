<?php
namespace Explorer;
class JDK {

    public static $tc;

    public static function getTopClient(){
        if (empty(self::$tc)) {
            $config = new \Yaf\Config\Ini(APPLICATION_PATH.'/conf/jdk.ini');
            self::$tc = new \TopClient();
            self::$tc->appkey = $config->jdk->appkey;
            self::$tc->secretKey = $config->jdk->secretkey;
            self::$tc->format = 'json';
            self::$tc->connectTimeout = 1;
            self::$tc->readTimeout = 2;
        }
        return self::$tc;
    }
}
