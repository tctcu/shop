<?php
/**
 * 商品更新 30 分钟更新一次
 *
 */
include('Common_func.php');
include('function.php');

for($min_id=1;$min_id<31;$min_id++) {//取 30*1000 数据更新

    $list_api = "http://v2.api.haodanku.com/update_item/apikey/allfree/sort/1/back/1000/min_id/" . $min_id;

    $list = get_curl($list_api);
    if (empty($list['data'])) {
        echo 'empty';
        exit;
    }

    foreach ($list['data'] as $val) {
        if(empty($val['itemid'])){
            continue;
        }
        $update = [
            'activityid' => $val['activityid'],
            'itemprice' => $val['itemprice'],
            'itemendprice' => $val['itemendprice'],
            'itemsale' => $val['itemsale'],
            'tkrates' => $val['tkrates'],
            'couponendtime' => $val['couponendtime'],
            'couponreceive' => $val['couponreceive'],
            'couponmoney' => $val['couponmoney'],
            'updated_at' => time()
        ];

        $update_sql = 'update tb set';
        foreach ($update as $k=>$v) {
            $update_sql .=  $k . "='" . $v . "',";
        }
        $update_sql = rtrim($update_sql, ",") . " where itemid =".$val['itemid'];
        $dbh->exec($update_sql);
    }
    echo $min_id."\n";

}
echo 'ok';die;
