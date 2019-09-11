<?php
/**
 * jd.kepler.service.promotion.goodsinfo(获取推广商品信息接口)
 */
class QueryPromotionGoodsInfoRequest {

    private $apiParams;

    public function setSku($sku) {
        $this->apiParams['skuIds'] = strval($sku);
    }

    public function getApiMethodName() {
        return 'jd.kepler.service.promotion.goodsinfo';
    }

    public function getApiParas() {
        return $this->apiParams;
    }

}



