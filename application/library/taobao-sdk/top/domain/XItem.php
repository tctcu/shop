<?php

/**
 * 返回结果
 * @author auto create
 */
class XItem
{
	
	/** 
	 * 商品叶子类目
	 **/
	public $cid;
	
	/** 
	 * 是否淘客商品
	 **/
	public $istk;
	
	/** 
	 * 聚划算活动结束时间，1970年到现在的毫秒数。如果不是聚划算商品，该值为空
	 **/
	public $ju_end;
	
	/** 
	 * 是否是聚划算商品,如果查询参数的fields里没有设置ju条件，该值为空
	 **/
	public $ju_item;
	
	/** 
	 * 聚划算参团价格，如果不是聚划算商品，该值为空
	 **/
	public $ju_price;
	
	/** 
	 * 聚划算活动开始时间，1970年到现在的毫秒数。如果不是聚划算商品，该值为空
	 **/
	public $ju_start;
	
	/** 
	 * 位置信息
	 **/
	public $location;
	
	/** 
	 * 是否天猫宝贝. true 是, false 不是
	 **/
	public $mall;
	
	/** 
	 * 卖家nick
	 **/
	public $nick;
	
	/** 
	 * 库存数量
	 **/
	public $num;
	
	/** 
	 * 混淆的商品ID(准备废弃，由open_iid代替)
	 **/
	public $open_auction_iid;
	
	/** 
	 * 废弃， 不使用了。
	 **/
	public $open_id;
	
	/** 
	 * 商品混淆ID
	 **/
	public $open_iid;
	
	/** 
	 * 主图链接
	 **/
	public $pic_url;
	
	/** 
	 * 平邮邮费. 单位:元,精确到分
	 **/
	public $post_fee;
	
	/** 
	 * 商品优惠价格(PC端),可能为空. 单位:元,精确到分。当PC端访问,且当前时间落在price_start_time到price_end_time区间内时使用该价格
	 **/
	public $price;
	
	/** 
	 * PC端商品优惠价格开始时间。如果当前没有PC端优惠，该字段为空
	 **/
	public $price_end_time;
	
	/** 
	 * PC端商品优惠价格结束时间。如果当前没有PC端优惠，该字段为空
	 **/
	public $price_start_time;
	
	/** 
	 * 手机端商品优惠价格. 可能为空。单位:元,精确到分。当手机端访问且当前时间落在price_wap_start_time到price_wap_end_time之间的话，使用该价格。如果改价格为空，请使用reserve_price.
	 **/
	public $price_wap;
	
	/** 
	 * 手机端商品优惠价格结束时间。如果当前没有手机端优惠，该字段为空
	 **/
	public $price_wap_end_time;
	
	/** 
	 * 手机端商品优惠价格开始时间。如果当前没有手机端优惠，该字段为空
	 **/
	public $price_wap_start_time;
	
	/** 
	 * 消保类型，多个类型以,分割。可取以下值： 2：假一赔三；4：7天无理由退换货；
	 **/
	public $promoted_service;
	
	/** 
	 * 商品的一口价
	 **/
	public $reserve_price;
	
	/** 
	 * 店铺名称
	 **/
	public $shop_name;
	
	/** 
	 * 商品标题
	 **/
	public $title;
	
	/** 
	 * 淘客佣金比例，比如：750 表示 7.50%
	 **/
	public $tk_rate;	
}
?>