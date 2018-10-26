<?php
#后台用户权限关系表
class AdminRolesModel extends MysqlModel {
    protected $_name = 'admin_roles';

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

    #删除
    function deleteByUid($uid = 0){
        if(empty($uid)){
            return false;
        }
        $row = $this->fetchRow("uid = {$uid}");
        if(empty($row)){
            return false;
        }
        return $this->delete("uid = {$uid}");
    }

    #通过uid查找信息
    function getAllByUid($uid = 0){
        if(empty($uid)){
            return false;
        }
        $sql = "select * from {$this->_name} where uid={$uid}";
        $data = $this->_db->fetchAll($sql);

        if(empty($data)){
            $data = array();
        }
        return $data;
    }


    function getListData($page = 1,$page_size =  20,$condition = array()){
        $sql = " select * from {$this->_name} where 1 ";
        if(!empty($condition['uid'])){
            $sql .= " and uid={$condition['uid']} ";
        }

        $start = ($page -1 ) * $page_size;
        $sql .= " limit {$start}, {$page_size}";
        try{
            $data = $this->_db->fetchAll($sql);
        }catch(Exception $ex){
            $data = array();
        }
        return $data;
    }

    #检查权限
    function checkAccess($uid, $module, $controller, $action){
        if(empty($uid) || empty($module) || empty($controller) || empty($action)){
            return false;
        }
        $sql = " select r.id, r.uid from admin_roles r, admin_access a where r. access_id=a.id and r.uid={$uid} and a.m='{$module}' and a.c='{$controller}' and a.a='{$action}'";

        $result = $this->_db->fetchRow($sql);
        if($result['id']) {
            return true;
        }
        return false;
    }


    #获取所有权限
    function getAccessList(){
        $sql = " select * from admin_access order by id asc ";

        try{
            $data = $this->_db->fetchAll($sql);
        }catch(Exception $ex){
            $data = array();
        }
        return $data;
    }
}