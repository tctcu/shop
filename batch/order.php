<?php
/**
 * 订单获取 5分钟一次
 * 改进 当订单超过100单/5分钟
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



$time = time()-300;//查300秒内的订单
$start_time = date('Y-m-d H:i:s',$time);
$all = 'trade_parent_id,trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk_status,tk3rd_type,tk3rd_pub_id,order_type,income_rate,pub_share_pre_fee,subsidy_rate,subsidy_type,terminal_type,auction_category,site_idString,site_name,adzone_id,adzone_name,alipay_total_price,total_commission_rate,total_commission_fee,subsidy_fee,relation_id,special_id,click_time';

$status = [//1: 全部订单（默认值），3：订单结算，12：订单付款， 13：订单失效，14：订单成功
    12 => 'pay',
    14 => 'success',
    3 => 'clearing',
    13 => 'lose',
];

$resp = [
    'session' => SESSION,
    'fields' => 'tb_trade_parent_id,tb_trade_id,site_id,adzone_id,alipay_total_price,income_rate,pub_share_pre_fee,num_iid,item_title,item_num,create_time,tk_status',
    'start_time' => $start_time,
    'span' => '300',//秒
    'page_size' => '100',
    'order_query_type' => 'create_time',
];
$url = 'http://gateway.kouss.com/tbpub/orderGet';

$dbh = dsn();
foreach($status as $k => $v){
    $resp['tk_status'] = $k;
    $resp = post_json_curl($url,$resp);
    if(isset($resp['tbk_sc_order_get_response']['results']['n_tbk_order']) && !empty($resp['tbk_sc_order_get_response']['results']['n_tbk_order'])) {
        foreach ($resp['tbk_sc_order_get_response']['results']['n_tbk_order'] as $val) {
            insertOrderLog($dbh,$val);
            if(empty($v($dbh,$val))){
                hdk_log($start_time.' '.$v.': '.json_encode($val));
            }
        }
    }
}

echo 'over';die;




#付款订单
function pay($dbh,$val){
    $date = [
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
        'terminal_type' => $val['terminal_type'],
        'trade_id' => $val['trade_id'],
        'trade_parent_id' => $val['trade_parent_id'],
        'created_at' => time(),
        'updated_at' => time()
    ];

    $insert_sql = "insert into tb_order(";
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

#订单成功
function success($dbh,$val){
    $date = [
        'adzone_id' => $val['adzone_id'],
        'site_id' => $val['site_id'],
        'rebate' => sprintf("%.2f",$val['pub_share_pre_fee'] * ConfigModel::REBATE),//订单返利
        'pub_share_pre_fee' => $val['pub_share_pre_fee'],
        'tk_status' => 14,
        'updated_at' => time()
    ];


    $update_sql = 'update tb_order set ';
    foreach ($date as $k=>$v) {
        $update_sql .=  $k . "='" . $v . "',";
    }
    $update_sql = rtrim($update_sql, ",") . " where trade_id =".$val['trade_id'];
    return $dbh->exec($update_sql);
}

#订单结算
function clearing($dbh,$val){
    $date = [
        'adzone_id' => $val['adzone_id'],
        'site_id' => $val['site_id'],
        'rebate' => sprintf("%.2f",$val['pub_share_pre_fee'] * ConfigModel::REBATE),//订单返利
        'pub_share_pre_fee' => $val['pub_share_pre_fee'],
        'tk_status' => 3,
        'updated_at' => time()
    ];


    $update_sql = 'update tb_order set ';
    foreach ($date as $k=>$v) {
        $update_sql .=  $k . "='" . $v . "',";
    }
    $update_sql = rtrim($update_sql, ",") . " where trade_id =".$val['trade_id'];
    return $dbh->exec($update_sql);
}

#查失效订单
function lose($dbh,$val){
    $date = [
        'rebate' => 0.00,//订单返利
        'pub_share_pre_fee' => 0.00,
        'tk_status' => 13,
        'updated_at' => time()
    ];


    $update_sql = 'update tb_order set ';
    foreach ($date as $k=>$v) {
        $update_sql .=  $k . "='" . $v . "',";
    }
    $update_sql = rtrim($update_sql, ",") . " where trade_id =".$val['trade_id'];
    return $dbh->exec($update_sql);
}


#订单记录
function insertOrderLog($dbh,$val){
    $date = [
        'type' => 1,//1-定时获取 2-每日汇总
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



