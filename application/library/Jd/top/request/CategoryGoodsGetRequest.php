<?php
/**
 * jd.union.open.category.goods.get 商品类目查询
 *
 * @author auto create
 * @since 1.0, 2019.07.04
 */
class CategoryGoodsGetRequest
{
	/**
	 * 父类目id(一级父类目为0)
	 **/
	private $parentId;

	/**
	 * 类目级别(类目级别 0，1，2 代表一、二、三级类目)
	 **/
	private $grade;
    private $reqParas = array();
	private $apiParas = array();

	public function setParentId($parentId)
	{
		$this->parentId = $parentId;
		$this->reqParas["parentId"] = $parentId;
	}

	public function getParentId()
	{
		return $this->parentId;
	}

	public function setGrade($grade)
	{
		$this->grade = $grade;
		$this->reqParas["grade"] = $grade;
	}

	public function getApiMethodName()
	{
		return "jd.union.open.category.goods.get";
	}

	public function getApiParas()
	{
        $this->apiParas['req'] = $this->reqParas;
		return $this->apiParas;
	}

	// public function check()
	// {

	// 	RequestCheckUtil::checkNotNull($this->text,"text");
	// 	RequestCheckUtil::checkNotNull($this->url,"url");
	// }

	public function putOtherTextParam($key, $value)
	{
		$this->$key = $value;
		$this->apiParas[$key] = $value;
	}
}
