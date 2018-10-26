<?php
/**
 * TOP API: taobao.baichuan.item.subscribe.relations.query request
 * 
 * @author auto create
 * @since 1.0, 2016.08.04
 */
class BaichuanItemSubscribeRelationsQueryRequest
{
	/** 
	 * 查询条件
	 **/
	private $condition;
	
	private $apiParas = array();
	
	public function setCondition($condition)
	{
		$this->condition = $condition;
		$this->apiParas["condition"] = $condition;
	}

	public function getCondition()
	{
		return $this->condition;
	}

	public function getApiMethodName()
	{
		return "taobao.baichuan.item.subscribe.relations.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
