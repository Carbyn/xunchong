<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlKpl', 'run']);

class CrawlKpl {

    public static function run() {
        $pkgList = \Explorer\JDKApis::getPkgList();
        // var_dump($pkgList);
        if ($pkgList->code != 0 || empty($pkgList->list)) {
            echo "getPkgList failed\n";
            return;
        }

        foreach($pkgList->list as $pkg) {
            $pageSize = 100;
            $maxPageNo = ceil($pkg->skuNum / $pageSize);
            for ($pageNo = 1; $pageNo <= $maxPageNo; $pageNo++) {
                $skuIdList = \Explorer\JDKApis::getSkuIdList($pkg->id, $pageSize, $pageNo);
                if ($skuIdList->code != 0 || empty($skuIdList->list)) {
                    echo "getSkuIdList failed\n";
                    continue;
                }
                foreach($skuIdList->list as $skuId) {
                    $baseInfo = \Explorer\JDKApis::queryProductBase($skuId);
                    if ($baseInfo->code != 0) {
                        echo "queryProductBase failed\n";
                        continue;
                    }

                    // TODO, not working
                    $thirtyDaySummary = \Explorer\JDKApis::queryThirtyDaySummary($skuId);
                    if (!$thirtyDaySummary->code != 0) {
                        echo "queryThirtyDaySummary failed\n";
                        continue;
                    }
                    if (!empty($thirtyDaySummary->sales)) {
                        var_dump($thirtyDaySummary);exit;
                    }

                    $promotionInfo = \Explorer\JDKApis::queryPromotionGoodsInfo($skuId);
                    // var_dump($promotionInfo);exit;
                    if (!$promotionInfo->code != 0) {
                        echo "queryPromotionGoodsInfo failed\n";
                        continue;
                    }

                    $actives = \Explorer\JDKApis::findJoinActives($skuId);
                    if ($actives->code != 0) {
                        echo "findJoinActives failed\n";
                    }
                    if (!empty($actives->coupons)) {
                        // var_dump($actives->coupons);
                        foreach($actives->coupons as $coupon) {
                            $urls = \Explorer\JDKApis::convertPromotionUrl($skuId, $coupon->toUrl);
                            var_dump($urls);exit;
                        }
                    }
                }
            }
        }
    }

}

