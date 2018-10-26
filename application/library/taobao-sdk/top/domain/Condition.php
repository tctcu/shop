<?php

/**
 * 删除条件
 * @author auto create
 */
class Condition
{
	
	/** 
	 * 结束时间
	 **/
	public $end_time;
	
	/** 
	 * 商品状态
	 **/
	public $item_status;
	
	/** 
	 * 删除个数，必填，若超过最大值报错
	 **/
	public $limit;
	
	/** 
	 * 对于删除，该字段无效
	 **/
	public $start_id;
	
	/** 
	 * 开始时间
	 **/
	public $start_time;	
}
?>