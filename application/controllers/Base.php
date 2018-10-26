<?php
abstract class BaseController extends Yaf_Controller_Abstract{

    protected $_request = null;
    protected $_module = null;
    protected $_controller = null;
    protected $_action = null;
    protected $_params = null;
    protected $_method = null;
    protected $_now = null;
    protected $_uri = null;

    /* @var $_error_codes System_ApiError() */
    protected $_error_codes = null;
    protected $_webkey = 'goodluck2huohuoproject018';

    // 初始化
    public function init(){
        $request = $this->getRequest();
        $this->_request = $request;
        $this->_module = $request->getModuleName();
        $this->_controller = $request->getControllerName();
        $this->_action = $request->getActionName();
        $this->_params = $request->getParams();
        $this->_method = $request->getMethod();
        $this->_now = time();
        $this->_uri = $_SERVER['REDIRECT_URL'];
        $this->_error_codes = new ErrorCodeModel();
    }

    /**
     * 发送原生 HTTP 头
     * @param mixed $header 头信息
     * @param boolean $replace
     * @param int $http_response_code
     */
    static protected function header($header, $replace = true, $http_response_code = null){
        if( is_string($header) )
            header($header, $replace, $http_response_code);
        if( is_array($header) ){
            foreach($header as $value){
                header(
                    $value[0],
                    isset($value[1]) ? $value[1] : $replace,
                    isset($value[2]) ? $value[2] : $http_response_code
                );
            }
        }
    }

    /**
     * 模板自动加载
     * @param boolean $flag
     */
    static protected function autoRender($flag){
        Yaf_Dispatcher::getInstance()->autoRender($flag);
    }

    /**
     * 获取输入参数 支持过滤和默认值
     * 使用方法:
     * <code>
     * I('id',0); 获取id参数 自动判断get或者post
     * I('post.name','','htmlspecialchars'); 获取$_POST['name']
     * I('get.'); 获取$_GET
     * </code>
     * @param string $name 变量的名称 支持指定类型
     * @param mixed $default 不存在的时候默认值
     * @param mixed $filter 参数过滤方法
     * @return mixed
     */
    static protected function I($name, $default = '', $filter = null){
        $default_filters = "addslashes,htmlspecialchars,trim";
        if(strpos($name, '.')) { // 指定参数来源
            list($method, $name) =   explode('.', $name, 2);
        }else{ // 默认为自动判断
            $method = 'param';
        }

        switch(strtolower($method)) {
            case 'get'     :   $input =& $_GET;break;
            case 'post'    :   $input =& $_POST;break;
            case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
            case 'param'   :
                switch($_SERVER['REQUEST_METHOD']){
                    case 'POST':
                        $input  =  $_POST;
                        break;
                    case 'PUT':
                        parse_str(file_get_contents('php://input'), $input);
                        break;
                    default:
                        $input  =  $_GET;
                }
                break;
            case 'request' :   $input =& $_REQUEST;   break;
            case 'session' :   $input =& $_SESSION;   break;
            case 'cookie'  :   $input =& $_COOKIE;    break;
            case 'server'  :   $input =& $_SERVER;    break;
            case 'globals' :   $input =& $GLOBALS;    break;
            default:
                return NULL;
        }

        if( empty($name) ){ // 获取全部变量
            $data       =   $input;
            $filters    =   isset($filter) ? $filter : $default_filters;
            if($filters) {
                $filters    =   explode(',', $filters);
                foreach($filters as $filter){
                    $data   =   array_map($filter, $data); // 参数过滤
                }
            }
        }elseif( isset($input[$name]) ){ // 取值操作
            $data       =	$input[$name];
            $filters    =   isset($filter)?$filter:$default_filters;
            if($filters) {
                $filters    =   explode(',',$filters);
                foreach($filters as $filter){
                    if(function_exists($filter)) {
                        $data   =   is_array($data)?array_map($filter,$data):$filter($data); // 参数过滤
                    }else{
                        $data   =   filter_var($data,is_int($filter)?$filter:filter_id($filter));
                        if(false === $data) {
                            return	 isset($default)?$default:NULL;
                        }
                    }
                }
            }
        }else{ // 变量默认值
            $data       =	 isset($default)?$default:NULL;
        }
        is_array($data) && array_walk_recursive($data,'filter');
        return $data;
    }

    /**
     * 发起http请求
     * @param string $url 请求地址
     * @param array $data 请求数据
     * @param string $type 请求类型 GET|POST
     * @param array $header 设置请求头信息
     * @return array [响应信息, 错误信息]
     */
    static protected function httpQuery($url, $data = array(), $type = 'GET', $header = array()){
        // 合并 GET 参数
        if( $type == 'GET' && !empty($data) ){
            $temp_arr = explode("?", urldecode($url));
            $query_arr = array();
            if( !empty($temp_arr[1]) ){
                $query_arr[] = $temp_arr[1];
            }
            $query_arr[] = http_build_query($data);
            $url = $temp_arr[0] . "?" . implode("&", $query_arr);
        }

        $curl = curl_init();
        // 不直接显示抓取结果
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // 设置url
        curl_setopt($curl, CURLOPT_URL, $url);
        // 判断是否是HTTPS的请求
        if( strpos($url, 'https://') === 0 ){
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        }
        // 设置请求类型
        if( $type == 'POST' ){
            curl_setopt($curl, CURLOPT_POST, true);
            if( !empty($data) )
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // 设置post数据
        }
        // 设置http头信息
        if( !empty($header) )
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        // 超时时间
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $response_string = curl_exec($curl);

        $curl_error = false;
        if( false === $response_string )
            $curl_error = curl_error($curl);
        curl_close($curl);
        // 返回 (响应信息、错误信息)
        return array($response_string, $curl_error);
    }

    /**
     * 创建目录
     * @param string $dir 目录地址
     * @return boolean 是否创建成功
     */
    static protected function mkDir($dir){
        if( is_dir($dir) ) return true;

        if( !mkdir($dir, 0777, true) ) return false;

        return true;
    }

    /**
     * 写日志
     * @param string $log_str 日志
     * @param string $filename 保存文件名
     * @param string $log_type 日志类型
     * @return void
     */
    static protected function logResult($log_str = '', $filename = 'huohuolog.txt', $log_type = '') {
        $log_dir = "/data/bak/log/";
        $fp = fopen($log_dir . $filename, "a");
        flock($fp, LOCK_EX) ;
        fwrite($fp, "执行日期：" . strftime("%Y%m%d%H%M%S",time()) . ", {$log_type}\n".$log_str."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * 来源地址
     * @return string|boolean
     */
    static protected function referer(){
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', 'REFERER'));
        if( !empty($_SERVER[$temp]) ) return $_SERVER[$temp];

        if( !function_exists('apache_request_headers') ) return false;

        $headers = apache_request_headers();
        if( empty($headers['REFERER']) ) return false;

        return $headers['REFERER'];
    }

    /**
     * 来源IP地址
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @return mixed
     */
    static protected function clientIp($type = 0){
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /*
     * 获取二维码
     */
    protected function qrcodeImageAction(){
        $qr_url = isset($_REQUEST['qr_url']) ? urldecode($_REQUEST['qr_url']) : '';
        include(APPLICATION_PATH.'/application/library/PhpQrCode.php');
        $errorCorrectionLevel = "L";// 纠错级别：L、M、Q、H
        $matrixPointSize = "4";// 点的大小：1到10
        //QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        QRcode::png($qr_url, false, $errorCorrectionLevel, $matrixPointSize , 0);
        exit;
    }

    /*
     * 生成随机字符串
     */
    protected function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];
        }
        return $str;
    }

    /*
     * 生成随机字符串
     */
    protected function catchException($error_msg = ''){
        if(empty($error_msg)){
            return 10001;
        }

        #匹配error_code
        preg_match('/(?<=error_code:-)[0-9]+/',$error_msg,$matches_code);

        if(!empty($matches_code)){
            return (int)$matches_code[0];
        }else{
            return 10001;
        }
    }

    #设置签名
    protected function makeWebSignature($timestamp = '', $nonce = ''){
        return hash_hmac('sha1', $nonce.$timestamp, $this->_webkey);
    }


    /**
     * 返回json数据
     * @param int $code 错误码
     * @param array $data 数据
     * @param string $msg 错误信息
     * @param int $options
     * @return void
     */
    public function responseJson($code = 0, $msg = '', $data = array(), $options = 0){
        $err_arr['return_code'] = strval($code);
        $err_arr['return_msg'] = !empty($msg) ? $msg : $this->_error_codes->system_errors[$code];
        $err_arr['return_data'] = $data;
        if( empty($data) && $options !=1) $options = JSON_FORCE_OBJECT;
        echo json_encode($err_arr, $options);
        exit;
    }

}