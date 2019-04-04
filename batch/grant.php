<?php
/**
 * 返利单发放
 *
 */
include('Common_func.php');
include('function.php');

$dbh = dsn();
//发放返利给用户
$sql = "select sum(rebate) as this_rebate,uid from tb_order where is_rebate=0 and tk_status=3 and is_final=1 and earning_time<>'0000-00-00 00:00:00' and uid<>0 group by uid";
$rebateList = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if(empty($rebateList)){
    hdk_log(date('Y-m-d H:i:s') . ' [发放返利]:empty');
    echo 'empty';die;
} else {
    hdk_log(date('Y-m-d H:i:s') . ' [发放返利]:'.json_encode($rebateList, JSON_UNESCAPED_UNICODE));
}
$time = time();
foreach ($rebateList as $row) {
    $select_sql = "select `use`,`total` from `user` where uid={$row['uid']}";
    $user_info = $dbh->query($select_sql)->fetch(PDO::FETCH_ASSOC);

    if ($user_info) {
        $use = $user_info['use'] + $row['this_rebate'];
        $total = $user_info['total'] + $row['this_rebate'];

        #关闭自动提交
        $dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        try {
            $dbh->beginTransaction();//开启事务处理

            #插入返利发放记录
            $insert_sql = "insert into account_record(uid,`type`,`before`,money,balance,created_at) VALUES({$row['uid']},1,{$user_info['use']},{$row['this_rebate']},{$use},{$time})";
            $affected_rows = $dbh->exec($insert_sql);
            if (!$affected_rows) {
                throw new PDOException("插入返利发放记录失败");
            }
            #操作返利金账户
            $update_user = "update `user` set `use`={$use},`total`={$total} where uid={$row['uid']}";
            $affected_rows = $dbh->exec($update_user);
            if (!$affected_rows) {
                throw new PDOException("更新用户金额失败");
            }
            #更新订单返利状态
            $update_order = "update tb_order set is_rebate=1 where uid={$row['uid']} and is_rebate=0 and tk_status=3 and is_final=1 and earning_time<>'0000-00-00 00:00:00'";
            $affected_rows = $dbh->exec($update_order);
            if (!$affected_rows) {
                throw new PDOException("活动记录状态更改失败");
            }
            $dbh->commit();//提交
        } catch (PDOException $e) {
            echo $e->getMessage() . 'UID' . $row['uid'] . "\n";
            $dbh->rollback();//回滚
        }
        #开启自动提交
        $dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }
}
hdk_log(date('Y-m-d H:i:s') . ' [发放返利]:over');
echo 'over';die;