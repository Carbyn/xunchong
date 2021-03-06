<?php
namespace Explorer;
class JD {

    const ITEM_URL = 'https://item.m.jd.com/product/%s.html';
    const PROMO_URL = 'https://wq.jd.com/commodity/promo/get?skuid=%s';
    const SFP_URL = 'https://p.3.cn/prices/mgets?&skuIds=J_%s&ext=11100000';
    const COUPON_URL = 'https://wq.jd.com/bases/couponsoa/avlCoupon?&cid=%d&popId=%s&sku=%s&platform=4';
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
        $promos = [];
        foreach($data['data'][0]['pis'] as $p) {
            unset($promo);
            if ($p['subextinfo']) {
                $subextinfo = json_decode($p['subextinfo'], true);
                switch($subextinfo['extType']) {
                case 1:
                    switch($subextinfo['subExtType']) {
                    case 1:
                        // 28486238350
                        $promo = [
                            'starttime' => (int)$p['st'],
                            'endtime'   => (int)$p['d'],
                        ];
                        foreach($subextinfo['subRuleList'] as $l) {
                            $promo['ext'][] = [
                                'needMoney' => (int)$l['needMoney'],
                                'rewardMoney' => (int)$l['rewardMoney'],
                            ];
                        }
                        $promos[\Constants::PROMO_MANJIAN][] = $promo;
                        break;
                    }
                    break;
                case 2:
                    switch($subextinfo['subExtType']) {
                    case 9:
                        $promo = [
                            'starttime' => (int)$p['st'],
                            'endtime'   => (int)$p['d'],
                            'ext' => [[
                                'needMoney' => (int)$subextinfo['needMoney'],
                                'rewardMoney' => (int)$subextinfo['rewardMoney'],
                            ]],
                        ];
                        $promos[\Constants::PROMO_MANJIAN][] = $promo;
                        break;
                    }
                    break;
                case 14:
                case 15:
                    switch($subextinfo['subExtType']) {
                    case 19:
                    case 23:
                        // 47203572367
                        $promo = [
                            'starttime' => (int)$p['st'],
                            'endtime'   => (int)$p['d'],
                        ];
                        foreach($subextinfo['subRuleList'] as $l) {
                            $promo['ext'][] = [
                                'needNum' => (int)$l['needNum'],
                                'rebate' => (float)$l['rebate'],
                            ];
                        }
                        $promos[\Constants::PROMO_ZHEKOU][] = $promo;
                        break;
                    }
                    break;
                default:
                }
            }
            if (isset($p['10'])) {
                // 47203572367
                $promos[\Constants::PROMO_ZENG] = [
                    'starttime' => (int)$p['st'],
                    'endtime' => (int)$p['d'],
                ];
            }
            if (isset($p['3'])) {
                $price = json_decode($p['customtag'], true);
                // 100000745034
                $promos[\Constants::PROMO_MIAOSHAJIA] = [
                    'starttime' => (int)$p['st'],
                    'endtime' => (int)$p['d'],
                    'ext' => [
                        'price' => (float)$price['p'],
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
                $promos[\Constants::PROMO_FENSIJIA] = [
                    'starttime' => 0,
                    'endtime' => 0,
                    'ext' => [
                        'price' => (float)$data[0]['sfp'],
                    ],
                ];
            }
            if (!empty($data[0]['tpp'])) {
                $promos[\Constants::PROMO_PLUSJIA] = [
                    'starttime' => 0,
                    'endtime' => 0,
                    'ext' => [
                        'price' => (float)$data[0]['tpp'],
                    ],
                ];
            }
        }

        $item_url = sprintf(self::ITEM_URL, $skuid);
        $html = Fetcher::getWithRetry($item_url);
        if ($html) {
            if (preg_match('/"venderID":"(\d+)"/', $html, $matches)) {
                $popId = $matches[1];
                $coupon_url = sprintf(self::COUPON_URL, $cid, $popId, $skuid);
                $headers['Referer'] = sprintf(self::REFERER, $skuid);
                $data = [];
                $c = 3;
                while($c-- > 0) {
                    $json = Fetcher::getWithRetry($coupon_url, $headers);
                    if ($json) {
                        $data = json_decode($json, true);
                        if ($data['ret'] == 0 && !empty($data['coupons'])) {
                            break;
                        }
                    }
                }
                foreach($data['coupons'] as $c) {
                    $times = explode(' - ', str_replace('.', '-', $c['timeDesc']));
                    if (count($times) == 2) {
                        $starttime = strtotime($times[0]);
                        $endtime = strtotime($times[1]) + 86400 - 1;
                    } else {
                        $starttime = 0;
                        $endtime = 0;
                    }
                    // 598283, 7003
                    $promos[\Constants::PROMO_COUPON][] = [
                        'starttime' => $starttime,
                        'endtime' => $endtime,
                        'ext' => [[
                            'needMoney' => $c['quota'],
                            'rewardMoney' => $c['discount'],
                        ]],
                    ];
                }
            }
        }

        return $promos;
    }

}
