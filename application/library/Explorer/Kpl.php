<?php
namespace Explorer;

class Kpl {

    public static function getToken($refresh = false) {
        $file = APPLICATION_PATH.'/data/kpl_token';
        if (!file_exists($file)) {
            return '';
        }
        $tokens = trim(file_get_contents($file));
        if (!$tokens) {
            return '';
        }
        $tokens = json_decode($tokens, true);
        if ($refresh) {
            return $tokens['refresh_token'];
        }
        return $tokens['access_token'];
    }

    public static function saveToken($access_token, $refresh_token) {
        $file = APPLICATION_PATH.'/data/kpl_token';
        $tokens = compact('access_token', 'refresh_token');
        file_put_contents($file, json_encode($tokens));
    }

}
