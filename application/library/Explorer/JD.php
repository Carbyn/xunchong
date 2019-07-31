<?php
namespace Explorer;
class JD {

    const PROMO_URL = 'https://wq.jd.com/commodity/promo/get?skuid=%s';
    const SFP_URL = 'https://p.3.cn/prices/mgets?&skuIds=J_%s&ext=11100000';

    public static function fetchPromo($skuid) {
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
                case 2:
                    switch($subextinfo['subExtType']) {
                    case 9:
                        $promo[\Constants::PROMO_MANJIAN][] = [
                            'needMoney' => (int)$subextinfo['needMoney'],
                            'rewardMoney' => (int)$subextinfo['rewardMoney'],
                        ];
                        break;
                    }
                    break;
                case 14:
                    switch($subextinfo['subExtType']) {
                    case 19:
                        foreach($subextinfo['subRuleList'] as $l) {
                            $promo[\Constants::PROMO_ZHEKOU][] = [
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
                $promo[\Constants::PROMO_ZENG] = 1;
            }
        }

        $sfp_url = sprintf(self::SFP_URL, $skuid);
        $json = Fetcher::getWithRetry($sfp_url);
        if ($json) {
            $data = json_decode($json, true);
            if (!empty($data[0]['sfp'])) {
                $promo[\Constants::PROMO_FENSIJIA] = [
                    'sfp' => (float)$data[0]['sfp'],
                ];
            }
        }
        return $promo;
    }

}
