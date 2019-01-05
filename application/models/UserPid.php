<?php
#用户跟单表
class UserPidModel extends MysqlModel {
    protected $_name = 'user_pid';

    function __construct(){
        parent::__construct();
    }

    #添加
    function addData($data){
        if(empty($data)){
            return false;
        }
        $data['created_at'] = time();
        $data['updated_at'] = time();
        return $this->insert($data);
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

    #根据pid查找单条信息
    function getDataByPid($site_id = 0 ,$adzone_id = 0){
        if(empty($site_id) || empty($adzone_id)){
            return false;
        }
        $sql = " select * from {$this->_name} where site_id = {$site_id} and adzone_id = {$adzone_id} limit 1";
        $result = $this->_db->fetchRow($sql);
        if(!empty($result)){
            return $result;
        }
        return false;
    }

    #绑定一个uid
    function bindUser($uid){
        $now = time();
        $sql = "update {$this->_name} set uid={$uid},updated_at={$now} where uid ='' limit 1";
        return $this->_db->exec($sql);
    }

    function getListData($page_size =  20,$condition = array()){
        $sql = " select * from {$this->_name} where 1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
        }

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

        $result = $this->_db->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }

}