<?php
/**
 * 订单获取 5分钟一次
 *
 */
include('Common_func.php');
include('function.php');

$time = time()-1000;//查1000秒内的订单
$start_time = date('Y-m-d H:i:s',$time);
$start_time = '2018-12-28 10:10:22';
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

if(isset($resp['tbk_sc_order_get_response']['results']['n_tbk_order']) && !empty($resp['tbk_sc_order_get_response']['results']['n_tbk_order'])){
    foreach($resp['tbk_sc_order_get_response']['results']['n_tbk_order'] as $val){
        $date = [
            'adzone_id' => $val['adzone_id'],
            'site_id' => $val['site_id'],
            'alipay_total_price' => $val['alipay_total_price'],
            'create_time' => $val['create_time'],
            'income_rate' => $val['income_rate'],
            'item_num' => $val['item_num'],
            'item_title' => $val['item_title'],
            'num_iid' => $val['num_iid'],
            'pub_share_pre_fee' => $val['pub_share_pre_fee'],
            'tk_status' => $val['tk_status'],
            'terminal_type' => $val['terminal_type'],
            'trade_id' => $val['trade_id'],
            'trade_parent_id' => $val['trade_parent_id'],
        ];

        $insert_sql = "insert into tb(";
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
            $update_sql = 'update tb_order set';
            foreach ($date as $k=>$v) {
                $update_sql .=  $k . "='" . $v . "',";
            }
            $update_sql = rtrim($update_sql, ",") . " where trade_id =".$val['trade_id'];
            $dbh->exec($update_sql);
        }

    }

} else {
    $retData = array();
}

return $retData;