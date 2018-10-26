<?php
/**
 * TOP API: taobao.baichuan.items.unsubscribe request
 * 
 * @author auto create
 * @since 1.0, 2016.08.04
 */
class BaichuanItemsUnsubscribeRequest
{
	/** 
	 * 删除的商品id
	 **/
	private $itemIds;
	
	private $apiParas = array();
	
	public function setItemIds($itemIds)
	{
		$this->itemIds = $itemIds;
		$this->apiParas["item_ids"] = $itemIds;
	}

	public function getItemIds()
	{
		return $this->itemIds;
	}

	public function getApiMethodName()
	{
		return "taobao.baichuan.items.unsubscribe";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->itemIds,"itemIds");
		RequestCheckUtil::checkMaxListSize($this->itemIds,100,"itemIds");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
