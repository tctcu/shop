<?php
Abstract class PdoModel{

    public $pdo = null;
    protected $_name = '';

    public function __construct()
    {
        $mysql_config = Yaf_Registry::get("config")->get('product.mysql.master.0');

        $this->pdo = "mysql:host=$mysql_config->host;dbname=$mysql_config->dbname";
        $this->pdo = new PDO($this->pdo, $mysql_config->username, $mysql_config->password);
        $this->pdo->query('set names utf8;');

    }

    function quoteInto($str,$arr){
        return $this->pdo->prepare($str,$arr);
    }


    function insert($data){
        echo  $this->_name;die;
    }

    function update($data,$id){

    }



    function fetchRow($sql){
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    function fetchAll($sql){
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function exec($sql){
        return $this->pdo->exec($sql);
    }





}
