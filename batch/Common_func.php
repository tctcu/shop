<?php
header('Content-type: text/html; charset=UTF-8');
error_reporting(E_ALL);ini_set('display_errors',1);
$dsn = "mysql:host=115.28.78.55;dbname=book";
$dbh = new PDO($dsn, 'root', 'mysql');
$dbh->query('set names utf8;');


class AliMemcache {
    protected $connect = null;
    function __construct(){
        $this->connect = new Memcached;
        $this->connect->setOption(Memcached::OPT_COMPRESSION, false);
        $this->connect->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
        $this->connect->addServer('d19236a3542e11e4.m.cnhzaliqshpub001.ocs.aliyuncs.com', 11211);
        $this->connect->setSaslAuthData('d19236a3542e11e4', 'f4c2_f782');
    }
    function getData($key) {
        return $this->connect->get($key);
    }
    function delData($key) {
        return $this->connect->delete($key);
    }
    function setData($key, $val, $expire = 0) {
        if(empty($expire)) {
            $expire =  time() + 3600;
        }
        return $this->connect->set($key, $val, $expire);
    }
    function quit(){
        return $this->connect->quit();
    }
}

class AliRedis {
    protected $redis = null;
    const TASK_LIST_QUEUE = 'task_list_queue_';
    CONST GAOE_TASK_QUEUE = 'gaoe_task_new_queue_';	// 高额任务队列,原 gaoe_task_list_queue_
    CONST ANDROID_TASK_QUEUE = 'android_task_queue_';	// 安卓任务列表
    CONST IOS_BUNDLE_ID_LIST = 'IOS_BUNDLE_ID_LIST_';	// IOS bundle_id 列表 后接 UID
    CONST BUNDLEID_WAIT_SYNC_LIST = 'BUNDLEID_WAIT_SYNC_LIST';	//set集合类型 bundle_id 等待同步数据库UID列表

    CONST PRAISE_TASK_QUEUE = 'praise_task_queue_';	// 好评任务队列

    function __construct(){
        $this->redis = new Redis();
        //连接服务器
        $this->redis->connect("r-bp1577a85a5ceeb4.redis.rds.aliyuncs.com");
        //授权
        $this->redis->auth("Fan4FF14");
        $this->redis->select("0");
    }

    private function getGaoeTaskListKey($gaoe_id)
    {
        return self::GAOE_TASK_QUEUE . $gaoe_id;
    }

    public function setGaoeCert($gaoe_id, $num, $tmout)
    {
        $key = $this->getGaoeTaskListKey($gaoe_id);

        $ret = $this->redis->incrBy($key, $num);
        if($ret <= $num){
            $this->redis->expire($key, $tmout);
        }

        return true;
    }

    /**
     * @param $task_id : 任务号
     * @param $oper : 操作类型:'get' - 获取;'set' - 设置
     * @return get操作：返回0-无凭证、>0凭证号;	set操作：返回true|false
     */
    public function operAndroidTaskAcce($task_id, $oper)
    {
        $key = self::ANDROID_TASK_QUEUE . $task_id;

        if($oper == 'get'){
            $ret = $this->redis->decr($key);

            if($ret >= 0){
                return ($ret+1);
            } else {
                $this->redis->incr($key);
                return 0;
            }
        } elseif($oper == 'set'){
            $this->redis->incr($key);
            return true;
        }
    }




    function getData($key) {
        return $this->redis->get($key);
    }
    function getList($key) {
        return $this->redis->LRANGE($key, 0, -1);
    }
    function getSize($task_id) {
        $key = self::TASK_LIST_QUEUE . $task_id;
        $size = $this->redis->lSize($key);
        if(!empty($size)) {
            return intval($size) ;
        } else {
            return 0;
        }

    }
    function delData($key) {
        return $this->redis->delete($key);
    }
    function setData($key, $val, $expire = 0) {
        if(empty($expire)) {
            $expire =  3600;
        }
        return $this->redis->setex($key, $expire, $val);
    }
    function push($task_id, $val) {
        $key = self::TASK_LIST_QUEUE . $task_id;
        return $this->redis->lPush($key, $val);
    }
    #将一个或多个值 value 插入到列表 key 的表尾(最右边)。
    function rPush($task_id, $val) {
        $key = self::TASK_LIST_QUEUE . $task_id;
        return $this->redis->rPush($key, $val);
    }
    function pop($task_id) {
        $key = self::TASK_LIST_QUEUE . $task_id;
        return $this->redis->rpop($key);
    }

    function setExpire($task_id, $tmout)
    {
        $key = self::TASK_LIST_QUEUE . $task_id;
        return $this->redis->expire($key, $tmout);
    }

    function getTaskRemain($task_id) {
        $key = self::TASK_LIST_QUEUE . $task_id;
        $size = $this->redis->lSize($key);
        if(!empty($size)) {
            return intval($size) ;
        } else {
            return 0;
        }
    }
    function getWaitSyncList()
    {
        $key = self::BUNDLEID_WAIT_SYNC_LIST;
        return $this->redis->sMembers($key);
    }
    /*function gettestlist()
    {
        $key = 'brush_ban_uid_set';
        $getlist = $this->redis->sMembers($key);
        foreach ($getlist as $val) {
            echo $val."\n";
        }
    }*/

    function delFromWaitSyncList($uid)
    {
        $key = self::BUNDLEID_WAIT_SYNC_LIST;
        return $this->redis->sRem($key, $uid);
    }

    function getUserApplist($uid)
    {
        $key = self::IOS_BUNDLE_ID_LIST . $uid;
        $getval = $this->redis->get($key);
        return $getval;
    }
    function lpushPraise($task_id, $val) {
        $key = self::PRAISE_TASK_QUEUE . $task_id;
        $this->redis->lPush($key, $val);
        return $this->redis->expire($key, 86400);
    }

    function lrangePraise($task_id) {
        $key = self::PRAISE_TASK_QUEUE . $task_id;
        return $this->redis->LRANGE($key, 0, -1);
    }

    function getlSize($key){
        return $this->redis->lSize($key);
    }

    function doLrem($key,$val){
        return $this->redis->lrem($key,$val);
    }
}