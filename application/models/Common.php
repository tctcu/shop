<?php
class CommonModel extends MysqlModel {
    protected $_name = 'common';

    const TYPE = [
        'session',//淘宝授权session
    ];//类型


    const IMAGE_URL = 'http://img.wzzsl.com/';//七牛地址
    const IMAGE_MIDDLE_SIZE = '?imageView2/1/w/300/h/300';
    const IMAGE_SMALL_SIZE = '?imageView2/1/w/150/h/150';

    const BIND_WE_CHAT = 'bind_wechat.jpg';//绑定微信授权二维码图


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

    #删除
    function deleteData($id){
        if(empty($id)){
            return false;
        }
        return $this->delete("id = {$id}");
    }

    #更新
    function updateData($data, $id){
        if(empty($data) || empty($id)){
            return false;
        }
        $data['updated_at'] = time();
        return $this->update($data,"id = {$id}");
    }

    #查找单条信息
    function getData($id = 0){
        if(empty($id)){
            return false;
        }
        $where = $this->_db->quoteInto('id = ?',$id);
        $data = $this->fetchRow($where);
        if(!empty($data)){
            return $data->toArray();
        }
        return false;
    }

    #查找信息
    function getDataByType($type = '',$key = 0){
        if(empty($type)){
            return false;
        }

        $sql = "select * from {$this->_name} where `type` = '{$type}'";
        if($key){
            return $this->_db->fetchRow($sql." and `key` = {$key}");
        }
        return $this->_db->fetchAll($sql);
    }

}
