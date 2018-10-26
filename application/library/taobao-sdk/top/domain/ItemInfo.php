<?php

/**
 * 商品信息
 * @author auto create
 */
class ItemInfo
{
	
	/** 
	 * true:商品可以卖，false:商品不可卖
	 **/
	public $in_sale;
	
	/** 
	 * 商品属性
	 **/
	public $item_props;
	
	/** 
	 * 商品图片，第一张主图
	 **/
	public $pics;
	
	/** 
	 * true:sku商品买家购买需要选择sku,false:买家购买不需要选择sku
	 **/
	public $sku_item;
	
	/** 
	 * 商品售卖时间，单位毫秒
	 **/
	public $start;
	
	/** 
	 * 商品子标题
	 **/
	public $sub_title;
	
	/** 
	 * 商品标题
	 **/
	public $title;	
}
?>