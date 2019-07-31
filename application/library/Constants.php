<?php
class Constants {

    const ERR_SYS_NOT_LOGGED       = 101;
    const ERR_SYS_ERROR            = 102;

    const ERR_LOGIN_MOBILE_INVALID = 1001;
    const ERR_LOGIN_SEND_FAILED    = 1002;
    const ERR_LOGIN_WRONG_CODE     = 1003;
    const ERR_LOGIN_WRONG_TOKEN    = 1004;
    const ERR_LOGIN_CODE_INVALID   = 1005;

    const ERR_USER_DATA_EMPTY      = 1101;

    const ERR_FEED_MOBILE_INVALID  = 1201;
    const ERR_FEED_ARTICLE_NOT_EXISTS = 1202;
    const ERR_FEED_UNAUTHORIZED    = 1203;
    const ERR_FEED_NO_IMAGE        = 1204;
    const ERR_FEED_UPLOAD_FAILED   = 1205;
    const ERR_FEED_TEXT_INVALID    = 1206;
    const ERR_FEED_EVENTTIME_INVALID = 1207;
    const ERR_FEED_EVENTADDRESS_INVALID = 1208;
    const ERR_FEED_TYPE_INVALID    = 1209;

    const ERR_ARTICLE_MOBILE_INVALID  = 1301;
    const ERR_ARTICLE_ARTICLE_NOT_EXISTS = 1302;
    const ERR_ARTICLE_UNAUTHORIZED    = 1303;
    const ERR_ARTICLE_NO_IMAGE        = 1304;
    const ERR_ARTICLE_UPLOAD_FAILED   = 1305;
    const ERR_ARTICLE_TEXT_INVALID    = 1306;
    const ERR_ARTICLE_EVENTTIME_INVALID = 1307;
    const ERR_ARTICLE_EVENTADDRESS_INVALID = 1308;
    const ERR_ARTICLE_TYPE_INVALID    = 1309;

    const ERR_COMMENT_ARTICLE_NOT_EXISTS = 1401;
    const ERR_COMMENT_TEXT_INVALID = 1402;
    const ERR_COMMENT_REPLY_AUTHOR_ID_INVALID = 1403;

    const ERR_LIKE_ARTICLE_NOT_EXISTS = 1501;

    const PROMO_MANJIAN = 'manjian';
    const PROMO_ZHEKOU  = 'zhekou';
    const PROMO_FENSIJIA = 'fensijia';
    const PROMO_ZENG     = 'zeng';

    private static $env;
    public static function env() {
        if (!self::$env) {
            self::$env = 'dev';
            $envPath = APPLICATION_PATH.'/.env';
            if (@file_exists($envPath)) {
                $envConfig = new \Yaf\Config\Ini($envPath);
                if ($envConfig->env == 'production') {
                    self::$env = $envConfig->env;
                }
            }
        }
        return self::$env;
    }

}
