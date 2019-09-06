<?php
/**
 * jd.kepler.xuanpin.getskuidlist(查询包内商品接口)
 */
class GetSkuIdListRequest {

    private $apiParams;

    public function setPkgId($pkgId) {
        $this->apiParams['pkgId'] = $pkgId;
    }

    public function setPageSize($pageSize) {
        $this->apiParams['pageSize'] = $pageSize;
    }

    public function setPageNo($pageNo) {
        $this->apiParams['pageNo'] = $pageNo;
    }

    public function getApiMethodName() {
        return 'jd.kepler.xuanpin.getskuidlist';
    }

    public function getApiParas() {
        return $this->apiParams;
    }

}

