<?php
/**
 * 订单获取 1天一次 第二天 刷一遍前一日订单
 *
 */
include('Common_func.php');
include('function.php');
require(dirname(dirname(__FILE__)) . '/application/models/Config.php'); //加载配置

require(dirname(dirname(__FILE__)) . '/application/library/taobao-sdk/TopSdk.php'); // 加载淘宝sdk
date_default_timezone_set('Asia/Shanghai');
$apiClient = new TopClient;

$apiClient->appkey = APPKEY;
$apiClient->secretKey = SECRETKEY;
$apiClient->format = 'json';


/*
    订单状态值，分别有：1: 全部订单（默认值），3：订单结算，12：订单付款， 13：订单失效，14：订单成功；注意：若订单查询类型参数order_query_type为“结算时间 settle_time”时，则本值只能查订单结算状态（即值为3）

Tip:

1、订单成功：表示买家确认收货，这时状态值是14.

2、订单结算：表示在买家确认收货后，联盟和卖家结算完佣金了。这时状态是3。也就是说14状态是在3前面。

注意这时的结算不是联盟和你结算，是和卖家结算。每个月20号联盟才跟你结算佣金。

3、订单失效：表示下了单但关闭订单等情形。
 * */

$yesterday = strtotime(date('Y-m-d 00:00:00', strtotime("-1 day")));
$today = strtotime(date('Y-m-d 00:00:00'));


if ($today - $yesterday <> 86400) {
    return 'error';
}


//$requ = [
//    'session' => SESSION,
//    'fields' => 'tb_trade_parent_id,tb_trade_id,site_id,adzone_id,alipay_total_price,income_rate,pub_share_pre_fee,num_iid,item_title,item_num,create_time,tk_status',
//    'span' => '1200',//秒
//    'page_size' => '100',
//    'order_query_type' => 'create_time',
//    'tk_status' => '1',
//];
$req = new TbkOrderGetRequest();
$req->setFields("tb_trade_parent_id,tb_trade_id,site_id,adzone_id,alipay_total_price,income_rate,pub_share_pre_fee,num_iid,item_title,item_num,create_time,tk_status");
$req->setSpan("1200");
$req->setPageSize("100");
$req->setTkStatus("1");
$req->setOrderQueryType("create_time");
$req->setOrderScene("1");//1-常规订单
//$req->setOrderCountType("1");

$dbh = dsn();
hdk_log(date('Y-m-d H:i:s') . ' [每日获取订单]:start');
for ($start = $yesterday; $start < $today; $start += 1200) {
    $start_time = date('Y-m-d H:i:s', $start);
    $page = 1;
    while (true) {
        $req->setStartTime("$start_time");
        $req->setPageNo("$page");
        $result = $apiClient->execute($req);
        $result = json_decode(json_encode($result),true);
        $resp['tbk_sc_order_get_response'] = $result;

        if (isset($resp['tbk_sc_order_get_response']['results'])) {
            if (isset($resp['tbk_sc_order_get_response']['results']['n_tbk_order']) && !empty($resp['tbk_sc_order_get_response']['results']['n_tbk_order'])) {
                $order_list = $resp['tbk_sc_order_get_response']['results']['n_tbk_order'];
                foreach ($order_list as $val) {
                    $data = [
                        'adzone_id' => $val['adzone_id'],
                        'site_id' => $val['site_id'],
                        'rebate' => sprintf("%.2f", $val['pub_share_pre_fee'] * ConfigModel::REBATE),//订单返利
                        'pub_share_pre_fee' => $val['pub_share_pre_fee'],
                        'tk_status' => $val['tk_status'],
                        'updated_at' => time()
                    ];

                    $select_sql = "select id,tk_status from tb_order where trade_id={$val['trade_id']}";
                    $order = $dbh->query($select_sql)->fetch(PDO::FETCH_ASSOC);
                    if ($order) {
                        if($order['tk_status'] == $val['tk_status']){
                            continue;
                        }
                        $update_sql = 'update tb_order set ';
                        foreach ($data as $k => $v) {
                            $update_sql .= $k . "='" . $v . "',";
                        }
                        $update_sql = rtrim($update_sql, ",") . " where id =" . $order['id'];
                        insertOrderLog($dbh, $val);
                        $dbh->exec($update_sql);
                    } else {
                        hdk_log(date('Y-m-d H:i:s') . ' [每日获取订单丢单]:' . $requ['start_time'] . json_encode($resp, JSON_UNESCAPED_UNICODE));

                        #订单关联用户
                        $select_sql = "select uid from user_pid where site_id={$val['site_id']} and adzone_id={$val['adzone_id']}";
                        $user_pid = $dbh->query($select_sql)->fetch(PDO::FETCH_ASSOC);
                        if($user_pid['uid']){
                            $data['uid'] = $user_pid['uid'];
                        }

                        $data['alipay_total_price'] = $val['alipay_total_price'];
                        $data['create_time'] = $val['create_time'];
                        $data['income_rate'] = $val['income_rate'] * 100;//单位%
                        $data['item_num'] = $val['item_num'];
                        $data['item_title'] = $val['item_title'];
                        $data['num_iid'] = $val['num_iid'];
                        $data['terminal_type'] = $val['terminal_type'];
                        $data['trade_id'] = $val['trade_id'];
                        $data['trade_parent_id'] = $val['trade_parent_id'];
                        $data['created_at'] = time();

                        $insert_sql = "insert into tb_order(";
                        foreach ($data as $k => $v) {
                            $insert_sql .= '`' . $k . '`,';
                        }
                        $insert_sql = rtrim($insert_sql, ",") . ') values(';

                        foreach ($data as $v) {
                            $insert_sql .= "'" . $v . "',";
                        }
                        $insert_sql = rtrim($insert_sql, ",") . ')';
                        insertOrderLog($dbh, $val);
                        $dbh->exec($insert_sql);
                    }
                }

                if (count($order_list) < 100) {
                    break 1;
                } else {
                    $page++;
                }
            } else {
                break 1;
            }
        } else {
            hdk_log(date('Y-m-d H:i:s') . ' [每日获取订单 api error]:' . $start_time . json_encode($resp, JSON_UNESCAPED_UNICODE));
            return 'error';
        }
    }
}

hdk_log(date('Y-m-d H:i:s') . ' [每日获取订单]:over');
echo 'over';die;


#订单记录
function insertOrderLog($dbh,$val){
    $date = [
        'json' => json_encode($val,JSON_UNESCAPED_UNICODE),
        'adzone_id' => $val['adzone_id'],
        'alipay_total_price' => $val['alipay_total_price'],
        'create_time' => $val['create_time'],
        'num_iid' => $val['num_iid'],
        'pub_share_pre_fee' => $val['pub_share_pre_fee'],
        'rebate' => sprintf("%.2f",$val['pub_share_pre_fee'] * ConfigModel::REBATE),//订单返利
        'tk_status' => $val['tk_status'],
        'trade_id' => $val['trade_id'],
        'created_at' => time()
    ];

    $insert_sql = "insert into tb_order_log(";
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
