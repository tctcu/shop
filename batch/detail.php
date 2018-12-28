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
$select_sql = "select itemid from tb where taobao_detail='' order by id asc limit 50";
$itemids = $dbh->query($select_sql)->fetchAll(PDO::FETCH_ASSOC);

    $num_iids  = implode(',', (array_column($itemids,'itemid')));

    $req = new TaeItemsListRequest;
    $req->setFields("promoted_service");
    $req->setNumIids("$num_iids");//批量
    $resp = $apiClient->execute($req);
    $resp = json_decode(json_encode($resp), true);

    if (isset($resp['items']['x_item']) && !empty($resp['items']['x_item'])) {
        $req = new TaeItemDetailGetRequest;
        $req->setBuyerIp("121.40.79.19");
        $req->setFields("mobileDescInfo");
        foreach($resp['items']['x_item'] as $val) {
            $t_iid = $val['open_iid'];
            $req->setOpenIid("$t_iid");
            // $req->setId("AAHUnBCNACaoGAiO6GJFE56X,AAFVnBCNACaoGAiO6GBIi3ZH");
            $resp = $apiClient->execute($req);
            $resp = json_decode(json_encode($resp), true);

            if (isset($resp['data']) && !empty($resp['data'])) {
                $taobao_detail = '';
                foreach($resp['data']['mobile_desc_info']['desc_list']['desc_fragment'] as $pic){
                    if($pic['label'] == 'img'){
                        $taobao_detail .= $pic['content'].',' ;
                    }
                }
                if($taobao_detail){
                    $taobao_detail = rtrim($taobao_detail,',');
                    $update_sql = "update tb set taobao_detail='{$taobao_detail}' where itemid={$val['open_id']}";
                    $dbh->exec($update_sql);
                }
            }
        }
    }

