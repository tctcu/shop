<?php
class MemcachedPlugin extends Yaf_Plugin_Abstract {
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        $memcached_config_obj = Yaf_Registry::get("config")->get('product.memcached');
        $memcached_obj = Yaf_Registry::get('memcached');

        if(empty($memcached_config_obj)){
            throw new Exception('the config of memcached can not be found');
        }

        if(empty($memcached_obj)) {

            $host_env = 'deploy';
            #$host_env = 'devel';
            try {

                $memcached = new Memcached;
                $memcached->setOption(Memcached::OPT_COMPRESSION, false);
                $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);

                $memcached->addServer($memcached_config_obj[$host_env]['host'], $memcached_config_obj[$host_env]['port']);

                if(!empty($memcached_config_obj[$host_env]['user']) && !empty($memcached_config_obj[$host_env]['password'])){
                    $memcached->setSaslAuthData($memcached_config_obj[$host_env]['user'], $memcached_config_obj[$host_env]['password']);
                }

            }catch(Exception $ex){
                exit($ex->getMessage());
            }

            Yaf_Registry::set("memcached",$memcached);

        }
    }

    public function postDispatch (Yaf_Request_Abstract $request , Yaf_Response_Abstract $response) {

    }
}
