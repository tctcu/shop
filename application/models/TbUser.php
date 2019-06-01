<?php
#用户淘宝授权表
class TbUserModel extends MysqlModel {
    protected $_name = 'tb_user';

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

    #更新
    function updateData($data, $uid){
        if(empty($data) || empty($uid)){
            return false;
        }
        $data['updated_at'] = time();
        return $this->update($data,"uid = {$uid}");
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

}