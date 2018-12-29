<?php
include('Common_func.php');
include('function.php');
require(dirname(dirname(__FILE__)) . '/application/library/taobao-sdk/TopSdk.php'); // 加载淘宝sdk
date_default_timezone_set('Asia/Shanghai');
$apiClient = new TopClient;

$apiClient->appkey = APPKEY;
$apiClient->secretKey = SECRETKEY;
$apiClient->format = 'json';


$dbh = dsn();
$select_sql = "select itemid from tb where taobao_detail='' and status=1 order by id desc limit 20";//批量最多50
$itemids = $dbh->query($select_sql)->fetchAll(PDO::FETCH_ASSOC);
$all_itemid = array_column($itemids,'itemid');
$n_iid = [];
$num_iids  = implode(',', $all_itemid);

$req = new TaeItemsListRequest;
$req->setFields("promoted_service");
$req->setNumIids("$num_iids");//批量
$resp = $apiClient->execute($req);
$resp = json_decode(json_encode($resp), true);

if (isset($resp['items']['x_item']) && !empty($resp['items']['x_item'])) {
    $req = new TaeItemDetailGetRequest;
    $req->setBuyerIp("121.40.79.19");
    $req->setFields("itemInfo,mobileDescInfo");
    foreach($resp['items']['x_item'] as $val) {
        $t_iid = $val['open_iid'];
        $req->setOpenIid("$t_iid");
        // $req->setId("AAHUnBCNACaoGAiO6GJFE56X,AAFVnBCNACaoGAiO6GBIi3ZH");
        $resp = $apiClient->execute($req);
        $resp = json_decode(json_encode($resp), true);

        if (isset($resp['data']) && !empty($resp['data'])) {
            $taobao_image = '';
            foreach($resp['data']['item_info']['pics']['string'] as $pic){
                $taobao_image .= $pic.',' ;
            }
            $taobao_detail = '';
            foreach($resp['data']['mobile_desc_info']['desc_list']['desc_fragment'] as $pic){
                if($pic['label'] == 'img'){
                    $taobao_detail .= $pic['content'].',' ;
                }
            }
            $update_sql = "update tb set ";
            $set = '';
            if($taobao_image){
                $taobao_image = rtrim($taobao_image,',');
                $set = " taobao_image='{$taobao_image}',";
            }
            if($taobao_detail){
                $taobao_detail = rtrim($taobao_detail,',');
                $set .= " taobao_detail='{$taobao_detail}',";


            }
            if($set){
                $set = rtrim($set,',');
                $update_sql = $update_sql . $set . " where itemid={$val['open_id']}";
                $res = $dbh->exec($update_sql);
                if($res) {
                    $n_iid[] = $val['open_id'];
                }
            }
        } else {
            return 'api error';exit;
        }
    }
}

$down_iid = array_diff($all_itemid,$n_iid);

if($down_iid){//下架没有详情页的数据 标记55
    $update_sql = "update tb set status = 55 where itemid in (".implode(',',$down_iid).")";
    $dbh->exec($update_sql);
}

