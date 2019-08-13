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
}
