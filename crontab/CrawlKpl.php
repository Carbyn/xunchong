<?php
include_once(dirname(__FILE__).'/Base.php');

$app->execute(['CrawlKpl', 'run']);

class CrawlKpl {

    public static function run() {
        $brandModel = new BrandModel();
        $brands = $brandModel->fetchAll();

        $pkgList = \Explorer\JDKApis::getPkgList();
        // var_dump($pkgList);
        if ($pkgList->code != 0 || empty($pkgList->list)) {
            echo "getPkgList failed\n";
            return;
        }

        foreach($pkgList->list as $pkg) {
            echo "{$pkg->desc} begin\n";
            $categories = self::parseCategory($pkg->desc);

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
                    $item = (array)$baseInfo->result;
                    $item['categories'] = $categories;
                    $item['brand_id'] = self::matchBrand($brands, $item['brandName']);

                    // TODO, not working
                    $thirtyDaySummary = \Explorer\JDKApis::queryThirtyDaySummary($skuId);
                    if ($thirtyDaySummary->code != 0) {
                        echo "queryThirtyDaySummary failed\n";
                        continue;
                    }
                    if (!empty($thirtyDaySummary->sales)) {
                        echo "thirtyDaySummary empty\n";
                        $item['volume'] = 0;
                    }

                    $promotionInfo = \Explorer\JDKApis::queryPromotionGoodsInfo($skuId);
                    if ($promotionInfo->code != 0) {
                        echo "queryPromotionGoodsInfo failed\n";
                        continue;
                    }
                    $promotionInfo = json_decode($promotionInfo->result, true);
                    if (empty($promotionInfo['result'])) {
                        echo "queryPromotionGoodsInfo failed\n";
                        continue;
                    }
                    $item['cid3'] = $promotionInfo['result'][0]['cid3'];
                    $item['commisionRatioWl'] = $promotionInfo['result'][0]['commisionRatioWl'];

                    $actives = \Explorer\JDKApis::findJoinActives($skuId);
                    if ($actives->code != 0) {
                        echo "findJoinActives failed\n";
                    }
                    if (!empty($actives->coupons)) {
                        foreach($actives->coupons as $coupon) {
                            if ($coupon->couponType > 1) {
                                continue;
                            }
                            $urls = \Explorer\JDKApis::convertPromotionUrl($skuId, $coupon->toUrl);
                            var_dump($urls);exit;
                            $coupon = [
                                'starttime' => $coupon->beginTime / 1000,
                                'endtime' => $coupon->endTime / 1000,
                                'ext' => [[
                                    'needMoney' => (int)$coupon->quota,
                                    'rewardMoney' => (int)$coupon->discount,
                                ]],
                            ];
                            $item['union_coupon_info'] = json_encode($coupon);
                            $item['coupon_click_url'] = $urls;
                        }
                    }
                }
            }
        }
    }

    private static function parseCategory($title) {
        $title = explode('_', $title);
        if (empty($title)) {
            echo "$title format unsupport\n";
            return false;
        }
        $category = explode('-', $title[0]);
        if (count($category) != 3) {
            echo "{$title} format unsupport\n";
            return false;
        }
        $categoryModel = new CategoryModel();
        $c = $categoryModel->fetchCatByPcidAndName(0, $category[0]);
        if (!$c) {
            echo "{$category[0]} not found\n";
            return false;
        }
        $cc = $categoryModel->fetchCatByPcidAndName($c->cid, $category[1]);
        if (!$cc) {
            echo "{$c->cid}, $category[1] not found\n";
            return false;
        }
        $ccc = $categoryModel->fetchCatByPcidAndName($cc->cid, $category[2]);
        if (!$ccc) {
            echo "{$cc->cid}, $category[2] not found\n";
            return false;
        }
        return [$c, $cc, $ccc];
    }

    private static function matchBrand($brands, $title) {
        foreach($brands as $b) {
            if (mb_strpos($title, $b['name']) !== false) {
                return $b['id'];
            }
        }
        return false;
    }
}
