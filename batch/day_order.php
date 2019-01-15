<?php
/**
 * 订单获取 1天一次 过了凌晨 汇总前一日订单
 * 改进 半天一次 或 更短 20分钟内超过100单无分页
 *
 */
include('Common_func.php');
include('function.php');
require(dirname(dirname(__FILE__)) . '/application/models/Config.php'); //加载配置



/*
    订单状态值，分别有：1: 全部订单（默认值），3：订单结算，12：订单付款， 13：订单失效，14：订单成功；注意：若订单查询类型参数order_query_type为“结算时间 settle_time”时，则本值只能查订单结算状态（即值为3）

Tip:

1、订单成功：表示买家确认收货，这时状态值是14.

2、订单结算：表示在买家确认收货后，联盟和卖家结算完佣金了。这时状态是3。也就是说14状态是在3前面。

注意这时的结算不是联盟和你结算，是和卖家结算。每个月20号联盟才跟你结算佣金。

3、订单失效：表示下了单但关闭订单等情形。
 * */

$yesterday = strtotime(date('Y-m-d 00:00:00',strtotime("-1 day")));
$today = strtotime(date('Y-m-d 00:00:00'));
if($today-$yesterday<>86400){
    return 'error';
}

$url = 'http://gateway.kouss.com/tbpub/orderGet';
$resp = [
    'session' => SESSION,
    'fields' => 'tb_trade_parent_id,tb_trade_id,site_id,adzone_id,alipay_total_price,income_rate,pub_share_pre_fee,num_iid,item_title,item_num,create_time,tk_status',
    'start_time' => $start_time,
    'span' => '1200',//秒
    'page_size' => '100',
    'order_query_type' => 'create_time',
    'tk_status' => '1',
];
$dbh = dsn();

for( $start = $yesterday; $start < $today; $start += 1200){
    $resp['start_time'] = date('Y-m-d H:i:s',$start);
    $resp = post_json_curl($url,$resp);
    if(isset($resp['tbk_sc_order_get_response']['results']['n_tbk_order']) && !empty($resp['tbk_sc_order_get_response']['results']['n_tbk_order'])) {
        foreach ($resp['tbk_sc_order_get_response']['results']['n_tbk_order'] as $val) {
            insertOrderLog($dbh,$val);
        }
    }
    sleep(60);
}

echo 'over';die;



#订单记录
function insertOrderLog($dbh,$val){
    $date = [
        'type' => 2,//1-定时获取 2-每日汇总
        'json' => json_encode($val),
        'adzone_id' => $val['adzone_id'],
        'site_id' => $val['site_id'],
        'alipay_total_price' => $val['alipay_total_price'],
        'create_time' => $val['create_time'],
        'income_rate' => $val['income_rate']*100,//单位%
        'item_num' => $val['item_num'],
        'item_title' => $val['item_title'],
        'num_iid' => $val['num_iid'],
        'pub_share_pre_fee' => $val['pub_share_pre_fee'],
        'rebate' => sprintf("%.2f",$val['pub_share_pre_fee'] * ConfigModel::REBATE),//订单返利
        'tk_status' => $val['tk_status'],
        'trade_id' => $val['trade_id'],
        'trade_parent_id' => $val['trade_parent_id'],
        'created_at' => time()
    ];


    $insert_sql = "insert into tb_order_list(";
    foreach ($date as $k => $v) {
        $insert_sql .= '`' . $k . '`,';
    }
    $insert_sql = rtrim($insert_sql, ",") . ') values(';

    foreach ($date as $v) {
        $insert_sql .= "'" . $v . "',";
    }
    $insert_sql = rtrim($insert_sql, ",") . ')';

    return $dbh->exec($insert_sql);
}



