<?php
namespace Explorer;
class Fetcher {

    public static function getWithRetry($url, $retry = 3) {
        $curl = new \Curl\Curl();
        while ($retry > 0) {
            $curl->get($url);
            if (!$curl->error) {
                return $curl->response;
            }
            $retry--;
        }
        return false;
    }

}
