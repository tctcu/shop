<?php
class MysqlCluster {

    private $_isUseCluster = false;
    private $_master_sn = 0;
    private $_slave_sn = 0;

    private $_linkHandle = array(
        'master' => array(),
        'slave' => array(),
    );

    public function __construct($_isUseCluster = false){
        $this->_isUseCluster = $_isUseCluster;
    }

    function getAdapter($isMaster=true){

        if($isMaster){
            return $this->_getMasterMysql();
        }else{
            return $this->_getSlaveMysql();
        }
    }

    public function addMysql($config = array(),$isMaster = true){

        $init_config_keys = array('host','username','password','dbname','charset');
        foreach ($init_config_keys as $key) {
            if(!isset($config[$key])){
                throw new Exception("the Params of Mysql Conf Is Wrong", 1);
            }
        }

        if($isMaster){
            $this->_linkHandle['master'][$this->_master_sn] = Zend_Db::factory('PDO_MYSQL', $config);
            ++ $this->_master_sn;
        }else{
            $this->_linkHandle['slave'][$this->_slave_sn] = Zend_Db::factory('PDO_MYSQL', $config);
            ++ $this->_slave_sn;
        }
    }

    private function _getSlaveMysql(){
        // 就一台 Slave 机直接返回
        if($this->_slave_sn <= 1){
            return $this->_linkHandle['slave'][0];
        }
        // 随机 Hash 得到 Slave 的句柄
        $hash = mt_rand(0,$this->_slave_sn - 1);
        return $this->_linkHandle['slave'][$hash];
    }

    private function _getMasterMysql(){
        if($this->_master_sn <= 1){
            return $this->_linkHandle['master'][0];
        }
        // 随机 Hash 得到 Slave 的句柄
        $hash = mt_rand(0,$this->_master_sn - 1);
        return $this->_linkHandle['master'][$hash];
    }

}