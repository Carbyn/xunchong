<?php
/**
 * jd.kepler.xuanpin.getpkglist 查询商品包编号接口
 *
 */
class GetPkgListRequest {

    private $mode;
    private $reqParas = array();
	private $apiParas = array();

    public function getApiMethodName() {
        return 'jd.kepler.xuanpin.getpkglist';
    }

    public function getApiParas() {
        return ['mode' => 0];
    }

	public function putOtherTextParam($key, $value) {
		$this->$key = $value;
		$this->apiParas[$key] = $value;
    }

}

