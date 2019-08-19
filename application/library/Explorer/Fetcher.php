<?php
namespace Explorer;
class Fetcher {

    public static function getWithRetry($url, $headers = [], $retry = 3) {
        $curl = new \Curl\Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        // $curl->setOpt(CURLOPT_SSLVERSION, 3);
        while ($retry > 0) {
            if (!empty($headers)) {
                foreach($headers as $key => $val) {
                    switch($key) {
                    case 'User-Agent':
                        $curl->setUserAgent($val);
                        break;
                    case 'Referer':
                        $curl->setReferer($val);
                        break;
                    case 'Cookie':
                        $curl->setCookie($val['key'], $val['val']);
                        break;
                    case 'Accept-Encoding':
                        $curl->setHeader($key, $val);
                        $curl->setOpt(CURLOPT_ENCODING, $val);
                        break;
                    default:
                        $curl->setHeader($key, $val);
                    }
                }
            }
            $curl->get($url);
            if (!$curl->error) {
                return $curl->response;
            }
            $retry--;
        }
        return false;
    }

    public static function postWithRetry($url, $data, $headers = [], $retry = 3) {
        $curl = new \Curl\Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        // $curl->setOpt(CURLOPT_SSLVERSION, 3);
        while ($retry > 0) {
            if (!empty($headers)) {
                foreach($headers as $key => $val) {
                    switch($key) {
                    case 'User-Agent':
                        $curl->setUserAgent($val);
                        break;
                    case 'Referer':
                        $curl->setReferer($val);
                        break;
                    case 'Cookie':
                        $curl->setCookie($val['key'], $val['val']);
                        break;
                    case 'Accept-Encoding':
                        $curl->setHeader($key, $val);
                        $curl->setOpt(CURLOPT_ENCODING, $val);
                        break;
                    default:
                        $curl->setHeader($key, $val);
                    }
                }
            }
            $curl->post($url, $data);
            if (!$curl->error) {
                return $curl->response;
            }
            $retry--;
        }
        return false;
    }

}
