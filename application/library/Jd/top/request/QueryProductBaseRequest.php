<?php
/**
 * public.product.base.query(商品基本信息查询)
 */
class QueryProductBaseRequest {

    private $apiParams;

    public function setSku($sku) {
        $this->apiParams['sku'] = $sku;
    }

    public function getApiMethodName() {
        return 'public.product.base.query';
    }

    public function getApiParas() {
        return $this->apiParams;
    }

}


