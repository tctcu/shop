<?php

/**
 * 返回的结果
 * @author auto create
 */
class ResultData
{
	
	/** 
	 * 删除订阅关系的个数，由于接口有数量限制，故 根据count==0来判断是否全部删除完毕
	 **/
	public $count;
	
	/** 
	 * 具体的商品id
	 **/
	public $data_list;	
}
?>