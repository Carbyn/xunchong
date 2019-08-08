<?php 
namespace Explorer;

class JDKApis {
    // category.goods.get
    public static function getGoodsCategory($parentId=0, $grade=0) {
        $req = new \CategoryGoodsGetRequest();
        $req->setParentId($parentId);
        $req->setGrade($grade);
        $rsp = Jd::getTopClient()->execute($req);
        var_dump($rsp);exit;
    }
}
