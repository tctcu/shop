<?php
/**
 * 返利单发放
 *
 */
include('Common_func.php');

$aid = '12';//活动ID
$money = '10';//返利金额
if($aid && $money) {
    $sql = "select t2.uid,t1.id from activity_record t1 LEFT JOIN user t2 ON t1.uid=t2.uid where t1.aid={$aid} and t1.type=1";

    $resGetItemList = $dbh->prepare($sql);
    $resGetItemList->execute();
    $time = time();
    while ($row = $resGetItemList->fetch(PDO::FETCH_ASSOC)) {
        $select_sql = "select money from user where uid={$row['uid']}";
        $user_info = $dbh->query($select_sql)->fetch(PDO::FETCH_ASSOC);

        if ($user_info) {
            $remain = $user_info['money'] + $money;

            #关闭自动提交
            $dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
            try{
                $dbh->beginTransaction();//开启事务处理

                #插入返利发放记录
                $insert_sql = "insert into user_exchange(uid,type,tid,money,remain,remark,created_at) VALUES({$row['uid']},1,{$row['id']},{$money},{$remain},'活动".$aid."返现".$money."元',{$time})";
                $affected_rows = $dbh->exec($insert_sql);
                if(!$affected_rows){
                    throw new PDOException("插入返利发放记录失败");
                }
                #操作返利金账户
                $update_user = "update user set money={$remain} where uid={$row['uid']}";
                $affected_rows = $dbh->exec($update_user);
                if(!$affected_rows) {
                    throw new PDOException("更新用户金额失败");
                }
                #更新订单状态
                $update_record = "update activity_record set type=3 where id={$row['id']}";
                $affected_rows = $dbh->exec($update_record);
                if(!$affected_rows){
                    throw new PDOException("活动记录状态更改失败");
                }
                $dbh->commit();//提交
               echo  "活动".$aid."返现".$money."元\n";
            }catch(PDOException $e){
               echo $e->getMessage().',活动记录ID'.$row['id']."\n";
                $dbh->rollback();//回滚
            }
            #开启自动提交
            $dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
        }

    }
}