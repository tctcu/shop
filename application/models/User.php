<?php
#用户表
class UserModel extends MysqlModel {
    protected $_name = 'user';

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

    #查找单条信息
    function getDataByMobile($mobile = 0){
        if(empty($mobile)){
            return false;
        }
        $where = $this->_db->quoteInto('mobile = ?',$mobile);
        $data = $this->fetchRow($where);
        if(!empty($data)){
            return $data->toArray();
        }
        return false;
    }

    #微信unionId查找单条信息
    function getDataByUnionId($unionId = ''){
        if(empty($unionId)){
            return false;
        }
        $where = $this->_db->quoteInto('w_unionid = ?',$unionId);
        $data = $this->fetchRow($where);
        if(!empty($data)){
            return $data->toArray();
        }
        return false;
    }

    function getListData($page = 1,$page_size =  20,$condition = array()){
        $sql = " select * from {$this->_name} where 1 ";
        if(!empty($condition['uid'])){
            $sql .= " and uid={$condition['uid']} ";
        }

        if(!empty($condition['mobile'])){
            $sql .= " and mobile={$condition['mobile']} ";
        }

        $sql .= " order by uid desc ";

        $start = ($page -1 ) * $page_size;
        $sql .= " limit {$start}, {$page_size}";
        try{
            $data = $this->_db->fetchAll($sql);
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

        if(!empty($condition['mobile'])){
            $sql .= " and mobile={$condition['mobile']} ";
        }

        $result = $this->_db->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }


    function is_email($email)
    {
        $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
        if (strpos($email, '@') !== false && strpos($email, '.') !== false)
        {
            if (preg_match($chars, $email))
            {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function is_mobile_phone ($mobile_phone)
    {
        $chars = "/^1[0-9]{10}$/";
        if(preg_match($chars, $mobile_phone))
        {
            return true;
        }
        return false;
    }

    //创建邀请码
    function uid2code($user_id) {
        $source_string = 'gn8FbQqrDT3HY6A5RaE9fhNt47ydGBe';
        $num = $user_id;
        $code = '';
        while ( $num > 0) {
            $mod = $num % 31;
            $code = $source_string[$mod].$code;
            $num = ($num - $mod) / 31;
        }

        if(empty($code[3])){
            $code = str_pad($code,4,'k',STR_PAD_LEFT);
        }

        return $code;
    }

    //邀请码解密
    function code2uid($code) {
        $source_string = 'gn8FbQqrDT3HY6A5RaE9fhNt47ydGBe';
        if (strrpos($code, 'k') !== false){
            $code = substr($code, strrpos($code, 'k')+1);
        }
        $len = strlen($code);
        $code = strrev($code);
        $num = 0;
        for ($i=0; $i < $len; $i++) {
            $num += strpos($source_string, $code[$i]) * pow(31, $i);
        }

        return $num;
    }
}