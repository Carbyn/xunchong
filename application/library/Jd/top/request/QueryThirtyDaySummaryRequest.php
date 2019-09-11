<?php
/**
 * jd.kpl.open.item.querythirtydaysummary(近30日销量)
 */
class QueryThirtyDaySummaryRequest {

    private $apiParams;

    public function setSku($sku) {
        $this->apiParams['sku'] = $sku;
    }

    public function getApiMethodName() {
        return 'jd.kpl.open.item.querythirtydaysummary';
    }

    public function getApiParas() {
        return $this->apiParams;
    }

}




