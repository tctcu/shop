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

    /**
     * Insert 插入
     * @param $data
     * @return Int
     */
    public function insert($data)
    {
        if(empty($data)){
            return false;
        }
        $strSql = "INSERT INTO `$this->_name` (`".implode('`,`', array_keys($data))."`) VALUES ('".implode("','", $data)."')";
        $this->execSql($strSql);

        return $this->pdo->lastInsertId();
    }

    /**
     * Delete 删除
     * @param $where
     * @return Int
     */
    public function delete($where)
    {
        if(empty($where)){
            return false;
        }
        $strSql = "DELETE FROM `$this->_name` WHERE $where";

        return $this->execSql($strSql);
    }

    /**
     * Insert 更新
     * @param $data
     * @param $where
     * @return Int
     */
    function update($data,$where){
        if(empty($data) || empty($where)){
            return false;
        }
        $strSql = '';
        foreach ($data as $key => $value) {
            $strSql .= ", `$key`='$value'";
        }
        $strSql = substr($strSql, 1);
        $strSql = "UPDATE `$this->_name` SET $strSql WHERE $where";

        return $this->execSql($strSql);
    }


    /**
     * Select 查一条数据
     * @param $where
     * @return array
     */
    function find($where){
        if(empty($where)){
            return false;
        }
        return $this->pdo->query("SELECT * FROM `$this->_name` WHERE ". $where ." limit 1")->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Select 查一条
     * @param $sql
     * @return array
     */
    function fetchRow($sql){
        if(empty($sql)){
            return false;
        }
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Select 查所有
     * @param $sql
     * @return array
     */
    function fetchAll($sql){
        if(empty($sql)){
            return false;
        }
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * exec 执行
     * @param $sql
     * @return array
     */
    function execSql($sql){
        if(empty($sql)){
            return false;
        }
        return $this->pdo->exec($sql);
    }

    /**
     * beginTransaction 事务开始
     */
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * commit 事务提交
     */
    public function commit()
    {
        $this->pdo->commit();
    }

    /**
     * rollback 事务回滚
     */
    public function rollback()
    {
        $this->pdo->rollback();
    }

    /**
     * prepare 预处理
     * @param $sql
     * @return object
     */
    public function prepareSql($sql){
        return $this->pdo->prepare($sql);
    }

    /**
     * execute 执行预处理
     * @param $presql
     * @return object
     */
    public function execute($presql){
        return $this->pdo->execute($presql);
    }


    /**
     * transaction 通过事务处理多条SQL语句
     * @param array $arraySql
     * @return Boolean
     */
    public function execTransaction($arraySql)
    {
        $result = true;
        $this->beginTransaction();
        foreach ($arraySql as $strSql) {
            if ($this->execSql($strSql) == 0){
                $result = false;
            }
        }
        if ($result) {
            $this->commit();
        } else {
            $this->rollback();
        }
        return $result;
    }


    /**
     * getPDOError 捕获PDO错误信息
     */
    private function getPDOError()
    {
        if ($this->pdo->errorCode() != '00000') {
            $arrayError = $this->pdo->errorInfo();
            throw new Exception('MySQL Error:'.$arrayError[2]);
        }
    }

}
