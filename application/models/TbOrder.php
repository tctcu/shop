<?php
#淘宝订单表
class TbOrderModel extends MysqlModel {
    protected $_name = 'tb_order';

    /*
     *

      订单状态值，分别有：1: 全部订单（默认值），3：订单结算，12：订单付款， 13：订单失效，14：订单成功；注意：若订单查询类型参数order_query_type为“结算时间 settle_time”时，则本值只能查订单结算状态（即值为3）

Tip:

1、订单成功：表示买家确认收货，这时状态值是14.

2、订单结算：表示在买家确认收货后，联盟和卖家结算完佣金了。这时状态是3。也就是说14状态是在3前面。

注意这时的结算不是联盟和你结算，是和卖家结算。每个月20号联盟才跟你结算佣金。

3、订单失效：表示下了单但关闭订单等情形。


     * */

    const ORDER_STATUS = [
        '3' => '已结算',//联盟和卖家结算完佣金 14->3
        '12' => '待收货',//订单付款
        '13' => '失效',//下了单但关闭订单等情形
        '14' => '待结算',//买家确认收货
    ];

    function __construct(){
        parent::__construct();
    }

    #查找单条信息
    function getDataByUid($uid = 0){
        if(empty($uid)){
            return false;
        }
        $where = $this->_db->quoteInto('uid = ?',$uid);
        $data = $this->fetchRow($where);
        if(!empty($data)){
            return $data->toArray();
        }
        return false;
    }

    function getListData($page_size =  20,$condition = array()){
        $sql = " select * from {$this->_name} where 1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
        }
        if(!empty($condition['status'])){
            $sql .= " and status={$condition['status']} ";
        }
        if(!empty($condition['site_id'])){
            $sql .= " and site_id={$condition['site_id']} ";
        }
        if(!empty($condition['adzone_id'])){
            $sql .= " and adzone_id={$condition['adzone_id']} ";
        }

        if(!empty($condition['min_id']) && $condition['min_id']>1){
            $sql .= " and id<{$condition['min_id']} ";
        }

        $sql .= " order by id desc ";

        $sql .= " limit {$page_size}";

        try{
            $data = $this->_db->fetchAll($sql);
        }catch(Exception $ex){
            $data = array();
        }
        return $data;
    }

    function getListCount($condition = array()){
        $sql = " select count(*) as num from {$this->_name} where 1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
        }
        if(!empty($condition['status'])){
            $sql .= " and status={$condition['status']} ";
        }
        if(!empty($condition['site_id'])){
            $sql .= " and site_id={$condition['site_id']} ";
        }
        if(!empty($condition['adzone_id'])){
            $sql .= " and adzone_id={$condition['adzone_id']} ";
        }

        $result = $this->_db->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }

    function makeOrder($list){
        $data = [];
        foreach($list as $key=>$val){
            if(empty($data['min_id']) || $val['id'] < $data['min_id']){
                $data['min_id'] = $val['id'];
            }
            $data['list'][] = [
                'trade_id' => $val['trade_id'],
                'itemid' => $val['num_iid'],
                'itemshorttitle' => $val['item_title'],
                'item_num' => $val['item_num'],
                'alipay_total_price' => $val['alipay_total_price'],
                'rebate' => sprintf("%.2f", TbModel::REBATE * $val['pub_share_pre_fee']),
                'tk_status' => $val['tk_status'],
                'status' => TbOrderModel::ORDER_STATUS[$val['tk_status']],
                'create_time' => $val['create_time'],
            ];
        }
        return $data;
    }
}