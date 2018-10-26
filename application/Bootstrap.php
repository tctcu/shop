<?php
class Bootstrap extends Yaf_Bootstrap_Abstract
{
    private $_config;

    public function _initBootstrap(){
        $this->_config = Yaf_Application::app()->getConfig();
    }

    public function _initConfig(){
        $config = new Yaf_Config_Ini(APPLICATION_COINFIG_FILE);
        Yaf_Registry::set("config", $config);
    }

    #注册memcache插件
    public function _initPlugin(Yaf_Dispatcher $dispatcher){
        $dispatcher->registerPlugin(new MemcachedPlugin());
    }

    #自动加载第3方类库
    public function _initIncludePath(){
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->_config->application->library);
    }

    #注册Zend的命名空间
    public function _initNamespaces(){
        Yaf_Loader::getInstance()->registerLocalNameSpace(array("Zend"));
    }

    #错误展示
    public function _initErrors(){
        error_reporting (1);
        ini_set('display_errors','On');
    }

    #自定义默认访问
    public function _initDefaultName(Yaf_Dispatcher $dispatcher){
        $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
    }

    #路由规则
    public function _initRoute(Yaf_Dispatcher $dispatcher){
        //$router = Yaf_Dispatcher::getInstance()->getRouter();
        //$r = new Yaf_Route_Rewrite('/logout',array('controller'=> 'Index','action'=>"logout"));
        //$router->addRoute('index.logout', $r);
    }

    #layout插件
    public function _initLayout(Yaf_Dispatcher $dispatcher){
        /*layout allows boilerplate HTML to live in /views/layout rather than every script*/

        #非API访问开启模板layout系统
        $layout = new LayoutPlugin('layout.phtml');
        Yaf_Registry::set('layout', $layout);
        /*add the plugin to the dispatcher*/
        $dispatcher->registerPlugin($layout);
    }


    #用户插件
    public function _initUser(Yaf_Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new UserPlugin());
    }

    #DB插件
    public function _initMysql(Yaf_Dispatcher $dispatcher){
        $dispatcher->registerPlugin(new MysqlClusterPlugin());
    }

}
