<?php
namespace Explorer;

class JDKApis {
    // category.goods.get
    public static function getGoodsCategory($parentId=0, $grade=0) {
        $req = new \CategoryGoodsGetRequest();
        $req->setParentId($parentId);
        $req->setGrade($grade);
        return JDK::getTopClient()->execute($req);
    }

    public static function getGoodsInfo($skuid) {
        $req = new \GoodsInfoQueryRequest();
        $req->setSkuIds($skuid);
        return JDK::getTopClient()->execute($req);
    }

    /******** KPL ***********/
    public static function getPkgList() {
        $req = new \GetPkgListRequest();
        return JDK::getTopClient()->execute($req, true);
    }

    public static function getSkuIdList($pkgId, $pageSize, $pageNo) {
        $req = new \GetSkuIdListRequest();
        $req->setPkgId($pkgId);
        $req->setPageSize($pageSize);
        $req->setPageNo($pageNo);
        return JDK::getTopClient()->execute($req, true);
    }

    public static function queryProductBase($sku) {
        $req = new \QueryProductBaseRequest();
        $req->setSku($sku);
        return JDK::getTopClient()->execute($req, true);
    }

    public static function queryPromotionGoodsInfo($sku) {
        $req = new \QueryPromotionGoodsInfoRequest();
        $req->setSku($sku);
        return JDK::getTopClient()->execute($req, true);
    }

    public static function queryThirtyDaySummary($sku) {
        $req = new \QueryThirtyDaySummaryRequest();
        $req->setSku($sku);
        return JDK::getTopClient()->execute($req, true);
    }

    public static function findJoinActives($sku) {
        $req = new \FindJoinActivesRequest();
        $req->setSku($sku);
        return JDK::getTopClient()->execute($req, true, '2.0');
    }

    // TODO, no permission
    public static function convertPromotionUrl($itemUrl, $couponUrl) {
        $req = new \ConvertPromotionUrlRequest();
        $req->setMaterialId($itemUrl);
        $req->setCouponUrl($couponUrl);
        return JDK::getTopClient()->execute($req, true);
    }

}
