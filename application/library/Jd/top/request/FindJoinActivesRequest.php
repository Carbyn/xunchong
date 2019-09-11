<?php
/**
 * jd.kpl.open.item.findjoinactives(查询商品关联优惠券（含券总数和已发数量）)
 */
class FindJoinActivesRequest {

    private $apiParams;

    public function setSku($sku) {
        $this->apiParams['sku'] = $sku;
    }

    public function getApiMethodName() {
        return 'jd.kpl.open.item.findjoinactives';
    }

    public function getApiParas() {
        return $this->apiParams;
    }

}



