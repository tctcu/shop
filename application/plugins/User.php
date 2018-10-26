<?php
class UserPlugin extends Yaf_Plugin_Abstract
{
    public function routerShutdown (Yaf_Request_Abstract $request , Yaf_Response_Abstract $response)
    {
        #非API实时请求时,开启SESSION,当API请求时关闭,不开启,提高性能
        if(strtolower($request->getModuleName()) != 'api'){
            Yaf_Session::getInstance()->start();
        }
    }
}

