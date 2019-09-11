<?php
/**
 * jd.kpl.open.promotion.converturl(券品二合一推广转换接口)
 */
class ConvertPromotionUrlRequest {

    private $apiParams = [
        'webId' => '0',
        'positionId' => 0,
    ];

    public function setMaterialId($materialId) {
        $this->apiParams['materialId'] = $materialId;
    }

    public function setCouponUrl($couponUrl) {
        $this->apiParams['couponUrl'] = $couponUrl;
    }

    public function getApiMethodName() {
        return 'jd.kpl.open.promotion.converturl';
    }

    public function getApiParas() {
        return $this->apiParams;
    }

}




