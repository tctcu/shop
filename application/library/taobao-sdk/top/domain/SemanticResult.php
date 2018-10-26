<?php

/**
 * 文本分析结果
 * @author auto create
 */
class SemanticResult
{
	
	/** 
	 * 文本ID
	 **/
	public $id;
	
	/** 
	 * 物流包裹 0-包裹正常, 1-包裹有破损, 空-没有包裹信息
	 **/
	public $logistics_package;
	
	/** 
	 * 物流服务 0-服务好, 1-服务差,  空-没有物流服务信息
	 **/
	public $logistics_service;
	
	/** 
	 * 物流速度 0-速度快, 1- 速度慢,  空-没有物流速度信息
	 **/
	public $logistics_speed;	
}
?>