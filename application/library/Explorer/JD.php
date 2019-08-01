<?php
namespace Explorer;
class JD {

    const PROMO_URL = 'https://wq.jd.com/commodity/promo/get?skuid=%s';
    const SFP_URL = 'https://p.3.cn/prices/mgets?&skuIds=J_%s&ext=11100000';
    const COUPON_URL = 'https://wq.jd.com/bases/couponsoa/avlCoupon?&cid=%d&popId=8888&sku=%s&platform=4';
    const REFERER = 'https://item.m.jd.com/product/%s.html';

    public static function fetchPromo($skuid, $cid = 0) {
        $promo_url = sprintf(self::PROMO_URL, $skuid);
        $json = Fetcher::getWithRetry($promo_url);
        if (!$json) {
            return false;
        }

        $json = trim($json);
        $subjson = substr($json, 9, -1);
        $subjson = preg_replace('#"hit":"[^"]+",#', '', $subjson);
        $data = json_decode($subjson, true);
        if ($data['errcode'] != 0) {
            return false;
        }
        $promo = [];
        foreach($data['data'][0]['pis'] as $p) {
            if ($p['subextinfo']) {
                $subextinfo = json_decode($p['subextinfo'], true);
                switch($subextinfo['extType']) {
                case 1:
                    switch($subextinfo['subExtType']) {
                    case 1:
                        // 28486238350
                        $promo[\Constants::PROMO_MANJIAN] = [
                            'starttime' => (int)$p['st'],
                            'endtime'   => (int)$p['d'],
                        ];
                        foreach($subextinfo['subRuleList'] as $l) {
                            $promo[\Constants::PROMO_MANJIAN]['ext'] = [
                                'needMoney' => (int)$l['needMoney'],
                                'rewardMoney' => (int)$l['rewardMoney'],
                            ];
                        }
                        break;
                    }
                    break;
                case 2:
                    switch($subextinfo['subExtType']) {
                    case 9:
                        $promo[\Constants::PROMO_MANJIAN] = [
                            'starttime' => (int)$p['st'],
                            'endtime'   => (int)$p['d'],
                        ];
                        $promo[\Constants::PROMO_MANJIAN]['ext'][] = [
                            'needMoney' => (int)$subextinfo['needMoney'],
                            'rewardMoney' => (int)$subextinfo['rewardMoney'],
                        ];
                        break;
                    }
                    break;
                case 14:
                    switch($subextinfo['subExtType']) {
                    case 19:
                        // 47203572367
                        $promo[\Constants::PROMO_ZHEKOU] = [
                            'starttime' => (int)$p['st'],
                            'endtime'   => (int)$p['d'],
                        ];
                        foreach($subextinfo['subRuleList'] as $l) {
                            $promo[\Constants::PROMO_ZHEKOU]['ext'][] = [
                                'needNum' => (int)$l['needNum'],
                                'rebate' => (int)$l['rebate'],
                            ];
                        }
                        break;
                    }
                    break;
                default:
                }
            }
            if (isset($p['10'])) {
                // 47203572367
                $promo[\Constants::PROMO_ZENG] = [
                    'starttime' => (int)$p['st'],
                    'endtime' => (int)$p['d'],
                ];
            }
            if (isset($p['3'])) {
                $price = json_decode($p['customtag'], true);
                $price = (float)$price['p'];
                // 100000745034
                $promo[\Constants::PROMO_MIAOSHAJIA] = [
                    'starttime' => (int)$p['st'],
                    'endtime' => (int)$p['d'],
                    'ext' => [
                        'price' => $price,
                    ],
                ];
            }
        }

        $sfp_url = sprintf(self::SFP_URL, $skuid);
        $json = Fetcher::getWithRetry($sfp_url);
        if ($json) {
            $data = json_decode($json, true);
            if (!empty($data[0]['sfp'])) {
                // 28486238350
                $promo[\Constants::PROMO_FENSIJIA] = [
                    'sfp' => (float)$data[0]['sfp'],
                ];
            }
            if (!empty($data[0]['tpp'])) {
                $promo[\Constants::PROMO_PLUSJIA] = [
                    'tpp' => (float)$data[0]['tpp'],
                ];
            }
        }

        $coupon_url = sprintf(self::COUPON_URL, $cid, $skuid);
        $headers['Referer'] = sprintf(self::REFERER, $skuid);
        $json = Fetcher::getWithRetry($coupon_url, $headers);
        if ($json) {
            $data = json_decode($json, true);
            if ($data['ret'] == 0 && !empty($data['coupons'])) {
                foreach($data['coupons'] as $c) {
                    $times = explode(' - ', str_replace('.', '-', $c['timeDesc']));
                    $starttime = strtotime($times[0]);
                    $endtime = strtotime($times[1]);
                    // 598283, 7003
                    $promo[\Constants::PROMO_COUPON][] = [
                        'starttime' => $starttime,
                        'endtime' => $endtime,
                        'ext' => [
                            'needMoney' => $c['quota'],
                            'rewardMoney' => $c['discount'],
                        ],
                    ];
                }
            }
        }

        return $promo;
    }

}
