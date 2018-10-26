<?php
/**
 * TOP API: taobao.baichuan.item.subscribe.daily.left.query request
 * 
 * @author auto create
 * @since 1.0, 2016.08.04
 */
class BaichuanItemSubscribeDailyLeftQueryRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "taobao.baichuan.item.subscribe.daily.left.query";
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
