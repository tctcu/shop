<?php
Abstract class MysqlModel extends Zend_Db_Table_Abstract{
	const MYSQL_MASTER = 1;
	const MYSQL_SLAVE = 0;

	private $adapter = null;
	protected $memcached = null;
	private $log = true;//日志开关

	public function __construct()
	{

		$this->adapter = Yaf_Registry::get('mysql_cluster');
		$this->memcached = Yaf_Registry::get('memcached');
		parent::__construct($this->adapter->getAdapter(self::MYSQL_SLAVE));
	}


	protected function setMaster($is_Master = 0)
	{
		if ($is_Master == self::MYSQL_MASTER) {
			$this->_setAdapter($this->adapter->getAdapter(self::MYSQL_MASTER));
		} else {
			$this->_setAdapter($this->adapter->getAdapter(self::MYSQL_SLAVE));
		}
	}

	protected function throwException($error_code = 0)
	{
		$error_msg = "error_code:{$error_code}";
		throw new Exception($error_msg);
	}




	/*
     * 图片
     */
	#图片上传
	function addPic($upload_file = 'upload_file',$remote_pic = ''){
		if(isset($_FILES[$upload_file]) && $_FILES[$upload_file]['error'] == 0 ){
			if(empty($remote_pic)) {
				$ext_name = substr(strrchr($_FILES[$upload_file]['name'],'.'),1);
				$remote_pic = uniqid(rand(), true) . "." . $ext_name;
			}
			$upload = new CloudFileSystem();
			$upload->upload_file($upload_file,$remote_pic);
			return $remote_pic;
		}
		return false;
	}
	#删除
	function delPic($remote_pic){
		$cloud_obj = new CloudFileSystem();
		return $cloud_obj->delete($remote_pic);
	}


	/*
     * memcache
     */
	function getMemcachedData($key)
	{
		return $this->memcached->get($key);
	}
	function setMemcachedData($key, $data, $expire = 0)
	{
		if (empty($expire)) {
			$expire = time() + 3600;
		}
		return $this->memcached->set($key, $data, $expire);
	}
	function deleteMemcached($key)
	{
		$this->memcached->delete($key);
	}

	/*
     * 事务
     */
	function beginTransaction()
	{
		return $this->_db->beginTransaction();
	}

	function commit()
	{
		return $this->_db->commit();
	}

	function rollBack()
	{
		return $this->_db->rollBack();
	}

	/*
     * DB操作异常日志
     */
	function logException($str)
	{
		if ($this->log) {
			$fp = fopen('/data/bak/log/db_exception.log', 'a');
			if ($fp) {
				$str = date("Y-m-d H:i:s", time()) . '  ==> ' . $str;
				fwrite($fp, $str . "\n");
				fclose($fp);
			}
		}
	}

}
