<?php

/**
 * 参数列表
 * @author auto create
 */
class SendMessageRequest
{
	
	/** 
	 * 模板上下文
	 **/
	public $context;
	
	/** 
	 * 延迟多少时间发送，单位毫秒
	 **/
	public $delay_time;
	
	/** 
	 * 设备ID
	 **/
	public $device_id;
	
	/** 
	 * 设备级别发送次数限制
	 **/
	public $device_limit;
	
	/** 
	 * 多少秒内，设备级别发送次数限制，如100秒内一个设备发送次数不超过5次
	 **/
	public $device_limit_in_time;
	
	/** 
	 * 域
	 **/
	public $domain;
	
	/** 
	 * 外部ID
	 **/
	public $external_id;
	
	/** 
	 * 手机号
	 **/
	public $mobile;
	
	/** 
	 * 手机号级别发送次数限制
	 **/
	public $mobile_limit;
	
	/** 
	 * 多少秒内，手机号级别发送次数限制
	 **/
	public $mobile_limit_in_time;
	
	/** 
	 * 会话ID
	 **/
	public $session_id;
	
	/** 
	 * 会话级别发送次数限制
	 **/
	public $session_limit;
	
	/** 
	 * 会话级别发送次数限制
	 **/
	public $session_limit_in_time;
	
	/** 
	 * 签名
	 **/
	public $signature;
	
	/** 
	 * 签名ID
	 **/
	public $signature_id;
	
	/** 
	 * 模板ID
	 **/
	public $template_id;	
}
?>