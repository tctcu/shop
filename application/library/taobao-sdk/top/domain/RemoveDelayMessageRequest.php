<?php

/**
 * 删除延迟消息
 * @author auto create
 */
class RemoveDelayMessageRequest
{
	
	/** 
	 * appKey
	 **/
	public $app_key;
	
	/** 
	 * 业务域
	 **/
	public $biz_type;
	
	/** 
	 * 延迟发送id
	 **/
	public $delay_task_id;
	
	/** 
	 * 用于场景隔离，如果外部id可以保证唯一，可以忽略此字段
	 **/
	public $domain;
	
	/** 
	 * 外部id，如果发送时指定了外部id，删除可以使用之前的id
	 **/
	public $external_id;
	
	/** 
	 * 用户id
	 **/
	public $user_id;	
}
?>