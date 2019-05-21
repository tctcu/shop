<?php

/**
 * PublisherOrderDto
 * @author auto create
 */
class PublisherOrderDto
{
	
	/** 
	 * 推广位管理下的推广位名称对应的ID，同时也是pid=mm_1_2_3中的“3”这段数字
	 **/
	public $adzone_id;
	
	/** 
	 * 推广位管理下的自定义推广位名称
	 **/
	public $adzone_name;
	
	/** 
	 * 推广者赚取佣金后支付给阿里妈妈的技术服务费用的比率
	 **/
	public $alimama_rate;
	
	/** 
	 * 技术服务费=结算金额*收入比率*技术服务费率。推广者赚取佣金后支付给阿里妈妈的技术服务费用
	 **/
	public $alimama_share_fee;
	
	/** 
	 * 买家拍下付款的金额（不包含运费金额）
	 **/
	public $alipay_total_price;
	
	/** 
	 * 通过推广链接达到商品、店铺详情页的点击时间
	 **/
	public $click_time;
	
	/** 
	 * 产品类型
	 **/
	public $flow_source;
	
	/** 
	 * 订单结算的佣金比率+平台的补贴比率
	 **/
	public $income_rate;
	
	/** 
	 * 商品所属的根类目，即一级类目的名称
	 **/
	public $item_category_name;
	
	/** 
	 * 商品id
	 **/
	public $item_id;
	
	/** 
	 * 商品图片
	 **/
	public $item_img;
	
	/** 
	 * 商品链接
	 **/
	public $item_link;
	
	/** 
	 * 商品数量
	 **/
	public $item_num;
	
	/** 
	 * 商品单价
	 **/
	public $item_price;
	
	/** 
	 * 商品标题
	 **/
	public $item_title;
	
	/** 
	 * 订单所属平台类型，包括天猫、淘宝、聚划算等
	 **/
	public $order_type;
	
	/** 
	 * 买家确认收货的付款金额（不包含运费金额）
	 **/
	public $pay_price;
	
	/** 
	 * 推广者的会员id
	 **/
	public $pub_id;
	
	/** 
	 * 结算预估收入=结算金额*提成。以买家确认收货的付款金额为基数，预估您可能获得的收入。因买家退款、您违规推广等原因，可能与您最终收入不一致。最终收入以月结后您实际收到的为准
	 **/
	public $pub_share_fee;
	
	/** 
	 * 付款预估收入=付款金额*提成。指买家付款金额为基数，预估您可能获得的收入。因买家退款等原因，可能与结算预估收入不一致
	 **/
	public $pub_share_pre_fee;
	
	/** 
	 * 从结算佣金中分得的收益比率
	 **/
	public $pub_share_rate;
	
	/** 
	 * 维权标签，0 含义为非维权 1 含义为维权订单
	 **/
	public $refund_tag;
	
	/** 
	 * 掌柜旺旺
	 **/
	public $seller_nick;
	
	/** 
	 * 店铺名称
	 **/
	public $seller_shop_title;
	
	/** 
	 * 媒体管理下的ID，同时也是pid=mm_1_2_3中的“2”这段数字
	 **/
	public $site_id;
	
	/** 
	 * 媒体管理下的对应ID的自定义名称
	 **/
	public $site_name;
	
	/** 
	 * 补贴金额=结算金额*补贴比率
	 **/
	public $subsidy_fee;
	
	/** 
	 * 平台给与的补贴比率，如天猫、淘宝、聚划算等
	 **/
	public $subsidy_rate;
	
	/** 
	 * 平台出资方，如天猫、淘宝、或聚划算等
	 **/
	public $subsidy_type;
	
	/** 
	 * 订单在淘宝拍下付款的时间
	 **/
	public $tb_paid_time;
	
	/** 
	 * 成交平台
	 **/
	public $terminal_type;
	
	/** 
	 * 订单创建的时间，该时间同步淘宝，可能会略晚于买家在淘宝的订单创建时间
	 **/
	public $tk_create_time;
	
	/** 
	 * 订单确认收货后且商家完成佣金支付的时间
	 **/
	public $tk_earning_time;
	
	/** 
	 * 二方：佣金收益的第一归属者； 三方：从其他淘宝客佣金中进行分成的推广者
	 **/
	public $tk_order_role;
	
	/** 
	 * 订单付款的时间，该时间同步淘宝，可能会略晚于买家在淘宝的订单创建时间
	 **/
	public $tk_paid_time;
	
	/** 
	 * 已付款：指订单已付款，但还未确认收货 已收货：指订单已确认收货，但商家佣金未支付 已结算：指订单已确认收货，且商家佣金已支付成功 已失效：指订单关闭/订单佣金小于0.01元，订单关闭主要有：1）买家超时未付款； 2）买家付款前，买家/卖家取消了订单；3）订单付款后发起售中退款成功；3：订单结算，12：订单付款， 13：订单失效，14：订单成功
	 **/
	public $tk_status;
	
	/** 
	 * 提成=收入比率*分成比率。指实际获得收益的比率
	 **/
	public $tk_total_rate;
	
	/** 
	 * 佣金金额=结算金额*佣金比率
	 **/
	public $total_commission_fee;
	
	/** 
	 * 佣金比率
	 **/
	public $total_commission_rate;
	
	/** 
	 * 买家通过购物车购买的每个商品对应的订单编号，此订单编号并未在淘宝买家后台透出
	 **/
	public $trade_id;
	
	/** 
	 * 买家在淘宝后台显示的订单编号
	 **/
	public $trade_parent_id;
	
	/** 
	 * unid
	 **/
	public $unid;	
}
?>