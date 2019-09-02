<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlKplToken', 'run']);

class CrawlKplToken {

    const KPL_OAUTH_URL = 'https://open-oauth.jd.com/oauth2/refresh_token?app_key=%s&app_secret=%s&grant_type=refresh_token&refresh_token=%s';

    public static function run() {
        $refreshToken = \Explorer\Kpl::getToken(true);
        if (!$refreshToken) {
            echo "CrawlKplToken refreshToken not exists\n";
            return;
        }

        $config = new \Yaf\Config\Ini(APPLICATION_PATH.'/conf/jdk.ini');

        $url = sprintf(self::KPL_OAUTH_URL, $config->jdk->appkey, $config->jdk->secretkey, $refreshToken);
        echo $url."\n";
        $data = \Explorer\Fetcher::getWithRetry($url);
        if (!$data) {
            echo "CrawlKplToken get failed. url=$url\n";
            return;
        }
        $data = json_decode($data, true);
        if (!isset($data['access_token']) || !isset($data['refresh_token'])) {
            echo "CrawlKplToken response failed. resp=".json_encode($data)."\n";
            return;
        }
        \Explorer\Kpl::saveToken($data['access_token'], $data['refresh_token']);
    }

}
