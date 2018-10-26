<?php

/**
 * 收货地址数据结构
 * @author auto create
 */
class ShippingAddress
{
	
	/** 
	 * 收货地址编号
	 **/
	public $address_id;
	
	/** 
	 * 创建邮费地址信息的时间
	 **/
	public $created;
	
	/** 
	 * 是否作为默认代收货地址
	 **/
	public $is_agent_default;
	
	/** 
	 * 是否作为默认收货地址
	 **/
	public $is_default;
	
	/** 
	 * 收货人地址信息
	 **/
	public $location;
	
	/** 
	 * 收货人移动电话号码
	 **/
	public $mobile;
	
	/** 
	 * 收货人固定电话号码
	 **/
	public $phone;
	
	/** 
	 * 收货人姓名
	 **/
	public $receiver_name;	
}
?>