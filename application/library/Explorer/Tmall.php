<?php
namespace Explorer;
class Tmall {

    const PROMO_URL = 'https://mdskip.taobao.com/core/initItemDetail.htm?itemId=%s';
    const COUPON_URL = 'https://detailskip.taobao.com/json/wap/tmallH5Desc.do?itemId=%s&sellerId=%s';
    const REFERER = 'https://detail.tmall.com/item.htm?id=%s';
    const UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3873.0 Safari/537.36';
    const COOKIE = 'Ps5sKeC3Jr3WVt87czgrMRZb9bH1IWljoRjXznwtDv+zj6zvZ5nSrUIj3AeK313qqGZ4dr4ZY7aW8Jgo1vCDLA==';
    const COOKIE_COUPON = 'UoH7KlIBRN37YQ==';

    public static function fetchPromo($skuid, $sellerid = 0) {
        // COUPON
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

        // PROMO
        $promo_url = sprintf(self::PROMO_URL, $skuid);
        $referer = sprintf(self::REFERER, $skuid);
        $headers = [
            'Referer' => $referer,
            'Cookie'  => ['key' => 'enc', 'val' => self::COOKIE],
            'User-Agent' => self::UA,
        ];
        // TODO
        $json = Fetcher::getWithRetry($promo_url, $headers);
        $json = '
            setMdskip
            ({"defaultModel":{"bannerDO":{"success":true},"buyerRestrictInfoDO":{"amountRestrictInfoMap":{"def":{"amountCanBuy":2,"restrictAmount":2,"restrictType":4}},"success":true},"deliveryDO":{"areaId":110105025,"deliveryAddress":"上海","deliverySkuMap":{"default":[{"arrivalNextDay":false,"arrivalThisDay":false,"forceMocked":false,"postage":"快递: 0.00 ","postageFree":false,"skuDeliveryAddress":"上海","type":0}]},"destination":"北京市","success":true},"detailPageTipsDO":{"crowdType":0,"hasCoupon":true,"hideIcons":false,"jhs99":false,"minicartSurprise":0,"onlyShowOnePrice":false,"priceDisplayType":4,"primaryPicIcons":[],"prime":false,"showCuntaoIcon":false,"showDou11Style":false,"showDou11SugPromPrice":false,"showDou12CornerIcon":false,"showDuo11Stage":0,"showJuIcon":false,"showMaskedDou11SugPrice":false,"success":true,"trueDuo11Prom":false},"doubleEleven2014":{"doubleElevenItem":false,"halfOffItem":false,"showAtmosphere":false,"showRightRecommendedArea":false,"step":0,"success":true},"extendedData":{"newShareGift":{}},"extras":{},"gatewayDO":{"changeLocationGateway":{"queryDelivery":true,"queryProm":false},"redirect":{},"success":true,"trade":{"addToBuyNow":{},"addToCart":{}}},"inventoryDO":{"hidden":false,"icTotalQuantity":246,"skuQuantity":{},"success":true,"totalQuantity":246,"type":1},"itemPriceResultDO":{"areaId":110100,"duo11Item":false,"duo11Stage":0,"extraPromShowRealPrice":false,"halfOffItem":false,"hasDPromotion":false,"hasMobileProm":false,"hasTmallappProm":false,"hiddenNonBuyPrice":false,"hideMeal":false,"priceInfo":{"def":{"areaSold":true,"onlyShowOnePrice":false,"price":"408.00","promotionList":[{"amountPromLimit":0,"amountRestriction":"","basePriceType":"IcPrice","canBuyCouponNum":0,"endTime":1569810857000,"extraPromTextType":0,"extraPromType":0,"limitProm":false,"postageFree":false,"price":"245.00","promType":"normal","start":false,"startTime":1558685861000,"status":2,"tfCartSupport":false,"tmallCartSupport":false,"type":"促销价","unLogBrandMember":false,"unLogShopVip":false,"unLogTbvip":false}],"sortOrder":0}},"queryProm":false,"success":true,"successCall":true,"tmallShopProm":[{"campaignId":8888352023,"campaignName":"爱肯拿1.8","endTime":"1569772800000","houseHoldFor618":false,"isFree":false,"promPlan":[{"msg":"满1件,领超值赠品（赠完即止）"},{"msg":"","unit":1,"detailMsg":[{"quantity":"1","method":{"giftPool":{"giftList":[{"picUrl":"//img.alicdn.com/imgextra/i3/2257681138/O1CN01CReB9b1KHFGe8kLOp_!!0-item_pic.jpg","blank":false,"originalPrice":8000,"price":0,"count":1,"name":"6罐珍致","id":"599168967757","title":"珍致猫罐头6罐（赠品）","selected":true,"skuId":0,"url":"https://detail.tmall.com/item.htm?id=599168967757","nameEscape":"6&#32592;&#29645;&#33268;"}],"selectNum":1,"selected":true},"man":"1"},"promotionLevel":1}]}],"promPlanMsg":["满1件,领超值赠品（赠完即止）"],"startTime":"1564366547000","warmUp":false}]},"memberRightDO":{"activityType":0,"level":0,"postageFree":false,"shopMember":false,"success":true,"time":1,"value":0.5},"miscDO":{"bucketId":6,"cartAni":false,"city":"北京","cityId":110100,"hasCoupon":false,"region":"朝阳区","regionId":110105,"rn":"d04b9497c929f135adc795b646a6aca9","smartBannerFlag":"top","success":true,"supportCartRecommend":false,"systemTime":"1564909634846","town":"大屯街道","townId":110105025},"progressiveInfoDO":{"double11Privilege":-1,"newPCInstallment":false,"period":[{"count":3,"couponPrice":0,"ratio":0.023},{"count":6,"couponPrice":0,"ratio":0.045},{"count":12,"couponPrice":0,"ratio":0.075}],"progressiveEnable":true,"rateMap":{"3":0.023,"6":0.045,"12":0.075},"showProgressivePlan":true,"skuTitle":"花呗分期（可选）","success":true,"tipType":-1,"tryBeforeBuy":false},"regionalizedData":{"success":true},"sellCountDO":{"sellCount":"1154","success":true},"servicePromise":{"has3CPromise":false,"servicePromiseList":[{"description":"商品支持正品保障服务","displayText":"正品保证","icon":"无","link":"//rule.tmall.com/tdetail-4400.htm","rank":-1},{"description":"极速退款是为诚信会员提供的退款退货流程的专享特权，额度是根据每个用户当前的信誉评级情况而定","displayText":"极速退款","icon":"//img.alicdn.com/bao/album/sys/icon/discount.gif","link":"//vip.tmall.com/vip/privilege.htm?spm=3.1000588.0.141.2a0ae8&priv=speed","rank":-1},{"description":"七天无理由退换","displayText":"七天无理由退换","icon":"//img.alicdn.com/tps/i3/T1Vyl6FCBlXXaSQP_X-16-16.png","link":"//pages.tmall.com/wow/seller/act/seven-day","rank":-1}],"show":true,"success":true,"titleInformation":[]},"soldAreaDataDO":{"currentAreaEnable":true,"success":true,"useNewRegionalSales":true},"tradeResult":{"cartEnable":true,"cartType":2,"miniTmallCartEnable":true,"startTime":1564818169000,"success":true,"tradeEnable":true},"userInfoDO":{"activeStatus":2,"companyPurchaseUser":false,"loginMember":false,"loginUserType":"buyer","nicker":"管世明","success":true,"userId":1041455987}},"isSuccess":true})';
        if (!$json) {
            return false;
        }
        $json = preg_replace('/ /', '', $json);
        $json = substr($json, 12, -1);
        $data = json_decode($json, true);
        if (!$data || !$data['isSuccess']) {
            return false;
        }

        if (!empty($data['defaultModel']['itemPriceResultDO']['priceInfo']['def']['promotionList'])) {
            foreach($data['defaultModel']['itemPriceResultDO']['priceInfo']['def']['promotionList'] as $l) {
                $promo = [
                    'starttime' => $l['startTime'] / 1000,
                    'endtime' => $l['endTime'] / 1000,
                    'ext' => [
                        'price' => (float)$l['price'],
                    ],
                ];
                $promos[\Constants::PROMO_CUXIAOJIA][] = $promo;
            }
        }
        if (!empty($data['defaultModel']['itemPriceResultDO']['tmallShopProm'])) {
            foreach($data['defaultModel']['itemPriceResultDO']['tmallShopProm'] as $tsp) {
                if (!empty($tsp['promPlan'])) {
                    foreach($tsp['promPlan'] as $pp) {
                        if (!empty($pp['detailMsg'])) {
                            foreach($pp['detailMsg'] as $dm) {
                                if (!empty($dm['method']['discount'])) {
                                    $promo = [
                                        'starttime' => $tsp['startTime'] / 1000,
                                        'endtime' => $tsp['endTime'] / 1000,
                                        'ext' => [[
                                            'needMoney' => (int)$dm['method']['man'],
                                            'rewardMoney' => (int)$dm['method']['discount']['quota'],
                                        ]],
                                    ];
                                    $promos[\Constants::PROMO_MANJIAN][] = $promo;
                                }
                                if (!empty($dm['method']['giftPool'])) {
                                    $promos[\Constants::PROMO_ZENG] = [
                                        'starttime' => $tsp['startTime'] / 1000,
                                        'endtime' => $tsp['endTime'] / 1000,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $promos;
    }

}
