<?php
/**
 * jd.union.open.goods.promotiongoodsinfo.query 获取推广商品信息接口
 *
 */
class GoodsInfoQueryRequest {

    private $skuIds;
    private $reqParas = array();
	private $apiParas = array();

    public function setSkuIds($skuIds) {
        $this->skuIds = $skuIds;
        $this->reqParas['skuIds'] = $skuIds;
    }

    public function getSkuIds() {
        return $this->skuIds;
    }

    public function getApiMethodName() {
        return 'jd.union.open.goods.promotiongoodsinfo.query';
    }

    public function getApiParas() {
        $this->apiParas = $this->reqParas;
        return $this->apiParas;
    }

	public function putOtherTextParam($key, $value) {
		$this->$key = $value;
		$this->apiParas[$key] = $value;
    }

}
