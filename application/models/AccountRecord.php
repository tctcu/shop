<?php
#资金记录表
//class AccountRecordModel extends MysqlModel {
class AccountRecordModel extends PdoModel {
    protected $_name = 'account_record';

    const ACCOUNT_RECORD_TYPE = [
        '1' => '返利发放',
        '2' => '提现申请',
        '3' => '提现到账',
        '4' => '提现失败',
    ];

    function __construct(){
        parent::__construct();
    }

    #添加
    function addData($data){
        if(empty($data)){
            return false;
        }
        $data['created_at'] = time();
        return $this->insert($data);
    }

    #查找单条信息
    function getDataByUid($uid = 0){
        if(empty($uid)){
            return false;
        }
        $where = $this->quoteInto('uid = ?',$uid);
        $data = $this->fetchRow($where);
        if(!empty($data)){
            return $data->toArray();
        }
        return false;
    }

    function getList($page_size =  20,$condition = array()){
        $sql = " select * from {$this->_name} where 1 ";
        if(!empty($condition['uid'])){
            $sql .= " and uid={$condition['uid']} ";
        }

        if(!empty($condition['min_id']) && $condition['min_id']>0){
            $sql .= " and id<{$condition['min_id']} ";
        }

        $sql .= " order by id desc ";

        $sql .= " limit {$page_size}";

        try{
            $data = $this->fetchAll($sql);
        }catch(Exception $ex){
            $data = array();
        }
        return $data;
    }


    function getListData($page = 1,$page_size = 20,$condition = array()){
        $sql = " select * from {$this->_name} where 1 ";
        if(!empty($condition['uid'])){
            $sql .= " and uid={$condition['uid']} ";
        }
        if(!empty($condition['type'])){
            $sql .= " and type={$condition['type']} ";
        }

        $sql .= " order by id desc ";

        $start = ($page -1 ) * $page_size;
        $sql .= " limit {$start}, {$page_size}";

        try{
            $data = $this->fetchAll($sql);
        }catch(Exception $ex){
            $data = array();
        }
        return $data;
    }

    function getListCount($condition = array()){
        $sql = " select count(*) as num from {$this->_name} where 1 ";
        if(!empty($condition['uid'])){
            $sql .= " and uid={$condition['uid']} ";
        }
        if(!empty($condition['type'])){
            $sql .= " and type={$condition['type']} ";
        }

        $result = $this->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }

    function makeAccountRecord($data){
        return [
            'type' => AccountRecordModel::ACCOUNT_RECORD_TYPE[$data['type']],
            'symbol' => in_array($data['type'],[1,4]) ? '+' : '-',
            'pay_type' => $data['pay_type'],
            'before' => $data['before'],
            'money' => $data['money'],
            'balance' => $data['balance'],
            'created_at' => $data['created_at']
        ];
    }

}