<?php
/**
 * 订单获取 5分钟一次
 * 改进 当订单超过100单/20分钟
 *
 */
include('Common_func.php');
include('function.php');

/*
    订单状态值，分别有：1: 全部订单（默认值），3：订单结算，12：订单付款， 13：订单失效，14：订单成功；注意：若订单查询类型参数order_query_type为“结算时间 settle_time”时，则本值只能查订单结算状态（即值为3）

Tip:

1、订单成功：表示买家确认收货，这时状态值是14.

2、订单结算：表示在买家确认收货后，联盟和卖家结算完佣金了。这时状态是3。也就是说14状态是在3前面。

注意这时的结算不是联盟和你结算，是和卖家结算。每个月20号联盟才跟你结算佣金。

3、订单失效：表示下了单但关闭订单等情形。
 * */



$time = time()-1000;//查1000秒内的订单
$start_time = date('Y-m-d H:i:s',$time);

$resp = [
    'session' => SESSION,
    'fields' => 'tb_trade_parent_id,tb_trade_id,site_id,adzone_id,alipay_total_price,income_rate,pub_share_pre_fee,num_iid,item_title,item_num,create_time,tk_status',
    'start_time' => $start_time,
    'span' => '1200',//秒
    'page_size' => '100',
    'tk_status' => '1',
    'order_query_type' => 'create_time',
];
$all = 'trade_parent_id,trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk_status,tk3rd_type,tk3rd_pub_id,order_type,income_rate,pub_share_pre_fee,subsidy_rate,subsidy_type,terminal_type,auction_category,site_idString,site_name,adzone_id,adzone_name,alipay_total_price,total_commission_rate,total_commission_fee,subsidy_fee,relation_id,special_id,click_time';
$url = 'http://gateway.kouss.com/tbpub/orderGet';
$resp = post_json_curl($url,$resp);
$dbh = dsn();
if(isset($resp['tbk_sc_order_get_response']['results']['n_tbk_order']) && !empty($resp['tbk_sc_order_get_response']['results']['n_tbk_order'])){
    foreach($resp['tbk_sc_order_get_response']['results']['n_tbk_order'] as $val){
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

        //单条
        $res = $dbh->exec($insert_sql);

        if(empty($res)){//更新
            unset($date['created_at'],$date['trade_id']);
            $update_sql = 'update tb_order set ';
            foreach ($date as $k=>$v) {
                $update_sql .=  $k . "='" . $v . "',";
            }
            $update_sql = rtrim($update_sql, ",") . " where trade_id =".$val['trade_id'];
            $dbh->exec($update_sql);
        }
    }

} else {
    echo  'empty';exit;
}

echo 'over';die;