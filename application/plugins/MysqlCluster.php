<?php
class MysqlClusterPlugin extends Yaf_Plugin_Abstract {
	public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response){
		$mysql_configs = Yaf_Registry::get("config")->get('product.mysql')->toArray();
	    $mysql_cluster = Yaf_Registry::get('mysql_cluster');

	    if(empty($mysql_configs)) {
	    	throw new Exception('the config of mysql can not be found in the appliction conf');
	    }

	    if(empty($mysql_cluster)) {
	    	$mysql_cluster = new MysqlCluster();
	    	foreach ($mysql_configs['master'] as $config) {
	    		$mysql_cluster->addMysql($config,1);
	    	}
	    	foreach ($mysql_configs['slave'] as $config) {
	    		$mysql_cluster->addMysql($config,0);
	    	}
	    }

	    Yaf_Registry::set('mysql_cluster',$mysql_cluster);
	}
}


