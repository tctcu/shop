<?php
/**
 * 商品下架
 *
 */
include('Common_func.php');
include('function.php');

$list_api = "http://v2.api.haodanku.com/get_down_items/apikey/allfree/start/0/end/23";

$list = get_curl($list_api);
if(empty($list['data'])){
    echo 'empty';exit;
}
$insert_sql = '';
foreach($list['data'] as $k=>$val){
    $status = $val['down_type']+1;//下架原因（1失效，2过期，3价格改变（券条件不符），4低佣，5自主下架,6用户删除，7拉黑下架，8举报下架）存表+1
    $update_sql = "update tb set status={$status} where itemid={$val['itemid']}";
    $dbh->exec($update_sql);
    echo $k."\n";
}

echo 'ok';die;
