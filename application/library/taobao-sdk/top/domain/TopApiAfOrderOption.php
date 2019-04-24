<?php

/**
 * 入参的对象
 * @author auto create
 */
class TopApiAfOrderOption
{
	
	/** 
	 * pageNo
	 **/
	public $page_no;
	
	/** 
	 * pagesize
	 **/
	public $page_size;
	
	/** 
	 * 处罚状态，0 正常，1 待处罚，2冻结
	 **/
	public $punish_status;
	
	/** 
	 * 渠道管理id, 父订单号/子订单号/relation_id/special_id 至少传一个
	 **/
	public $relation_id;
	
	/** 
	 * 查询时间跨度，不超过1小时，单位是秒
	 **/
	public $span;
	
	/** 
	 * 会员运营id,父订单号/子订单号/relation_id/special_id 至少传一个
	 **/
	public $special_id;
	
	/** 
	 * 查询开始时间，以taoke订单创建时间开始
	 **/
	public $start_time;
	
	/** 
	 * 子订单号,父订单号/子订单号/relation_id/special_id 至少传一个
	 **/
	public $tb_trade_id;
	
	/** 
	 * 父订单号,父订单号/子订单号/relation_id/special_id 至少传一个
	 **/
	public $tb_trade_parent_id;
	
	/** 
	 * 处罚类型：1 店铺淘客，2其他
	 **/
	public $violation_type;	
}
?>