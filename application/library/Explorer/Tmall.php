<?php
namespace Explorer;
class Tmall {

    const PROMO_URL = 'https://mdskip.taobao.com/core/initItemDetail.htm?itemId=%s';
    const COUPON_URL = 'https://detailskip.taobao.com/json/wap/tmallH5Desc.do?itemId=%s&sellerId=%s';
    const REFERER = 'https://detail.tmall.com/item.htm?id=%s';
    const UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3873.0 Safari/537.36';
    const COOKIE = 'Ps5sKeC3Jr3WVt87czgrMRZb9bH1IWljoRjXznwtDv+zj6zvZ5nSrUIj3AeK313qqGZ4dr4ZY7aW8Jgo1vCDLA==';
    const COOKIE_COUPON = 'UoH7KlIBRN37YQ==';

    const H5_PROMO_URL = 'https://h5api.m.taobao.com/h5/mtop.taobao.detail.getdetail/6.0/?type=json&data=%s&t=%s';

    public static function fetchPromo($skuid, $sellerid = 0) {
        // PROMO
        $data = ['itemNumId' => "$skuid"];
        $data = [
            'id' => $skuid,
            'itemNumId' => $skuid,
            'exParams' => json_encode(['id' => $skuid]),
            'detail_v' => '8.0.0',
            'utdid' => '1',
        ];
        $t = intval(microtime(true) * 1000);
        $promo_url = sprintf(self::H5_PROMO_URL, urlencode(json_encode($data)), $t);
        $headers = [
            'Accept-Encoding' => 'gzip, deflate, br',
            'Cookie' => ['key' => 'munb', 'val' => '1041455987'],
        ];
        $json = Fetcher::getWithRetry($promo_url, $headers);
        // var_dump($json);
        if (!$json) {
            return false;
        }

        $json = trim($json);
        $data = json_decode($json, true);
        if (!$data || $data['ret'][0] != 'SUCCESS::调用成功') {
            return false;
        }
        $data = json_decode($data['data']['apiStack'][0]['value'], true);
        if (!$data) {
            return false;
        }
        if (!empty($data['price']['shopProm'])) {
            foreach($data['price']['shopProm'] as $sp) {
                $dates = explode('-', $sp['period']);
                $starttime = strtotime(str_replace('.', '-', $dates[0]));
                $endtime = strtotime(str_replace('.', '-', $dates[1]));
                if (!empty($sp['giftOfContent'])) {
                    $promos[\Constants::PROMO_ZENG] = [
                        'starttime' => $starttime,
                        'endtime' => $endtime,
                    ];
                } else if (!empty($sp['content'])) {
                    if (preg_match('/满(\d+)元,省(\d+)元/', $sp['content'][0], $matches)) {
                        $promos[\Constants::PROMO_MANJIAN][] = [
                            'starttime' => $starttime,
                            'endtime' => $endtime,
                            'ext' => [[
                                'needMoney' => (float)$matches[1],
                                'rewardMoney' => (float)$matches[2],
                            ]],
                        ];
                    }
                }
            }
        }
        if (!empty($data['price']['transmitPrice'])) {
            $promos[\Constants::PROMO_CUXIAOJIA] = [
                'starttime' => time(),
                // todo
                'endtime' => time() + 86400 * 2,
                'ext' => [
                    'price' => (float)$data['price']['transmitPrice']['priceText'],
                ],
            ];
        }

        return $promos;

        // COUPON
        // TODO
        $coupon_url = sprintf(self::COUPON_URL, $skuid, $sellerid);
        $headers = [
            'Cookie'  => ['key' => 'cookie17', 'val' => self::COOKIE_COUPON],
            'User-Agent' => self::UA,
        ];
        $json = Fetcher::getWithRetry($coupon_url, $headers);
        if (!$json) {
            return false;
        }
        $json = iconv('gbk', 'utf8', trim($json));
        $json = substr($json, 1, -1);
        $data = json_decode($json, true);
        if (!$data) {
            return false;
        }
        if (!empty($data[0]['data'])) {
            foreach($data[0]['data'] as $c) {
                if (!preg_match('/\d+/', $c['tips'], $matches)) {
                    continue;
                }
                $needMoney = (int)$matches[0];
                $rewardMoney = (int)$c['price'];
                $starttime = strtotime(str_replace('.', '-', $c['startDate']));
                $endtime = strtotime(str_replace('.', '-', $c['endDate'])) + 86400 - 1;
                $promos[\Constants::PROMO_COUPON][] = [
                    'starttime' => $starttime,
                    'endtime' => $endtime,
                    'ext' => [
                        'needMoney' => $needMoney,
                        'rewardMoney' => $rewardMoney,
                    ],
                ];
            }
        }

        return $promos;
    }

}
