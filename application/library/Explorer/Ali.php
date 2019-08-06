<?php
namespace Explorer;
class Ali {

    public static $tc;

    public static function getTopClient(){
        if (empty(self::$tc)) {
            $config = new \Yaf\Config\Ini(APPLICATION_PATH.'/conf/tbk.ini');
            self::$tc = new \TopClient();
            self::$tc->appkey = $config->tbk->appkey;
            self::$tc->secretKey = $config->tbk->secretkey;
        }
        return self::$tc;
    }

}
