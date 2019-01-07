<?php
abstract class ApiController extends BaseController
{
    const SUCCESS_CODE = '200';
    const SUCCESS_MSG = 'success';

    /* @var $error_codes System_ApiError() */
    protected $error_codes = null;
    protected $aes_key = null;
    protected $is_audit = 0;

    protected $uid = 0;

    function init(){
        parent::init();
        //解密
       if(1==2) {
           $data = !empty($_REQUEST['data']) ? addslashes(htmlspecialchars(trim(urldecode($_REQUEST['data'])))) : "";

           $params = !empty($_REQUEST['params']) ? addslashes(htmlspecialchars(trim(urldecode($_REQUEST['params'])))) : "";

           #rsa解密
           $rsa_model = new Rsa();
           $key_time = $rsa_model->decrypt($params);   //得到随机字符串和10位时间戳

           if (empty($key_time)) {  //rsa解码错误
               $code = 10025;
               $msg = $this->_error_codes->system_errors[$code];
               $this->responseJson($code, $msg);
               exit;
           }

           $key = substr($key_time, 0, 16);  //截取前16位随机参数
           $time = substr($key_time, -10, 10);  //截取后10位时间戳

//				if(time() - $time > 60*2){  //加密时间戳两分钟内有效
//					$code = 10021;
//					$msg = $this->_error_codes->system_errors[$code];
//					$this->echo_message($msg,$code);
//					exit;
//				}

           #进行ase解密，128位，测ecb模式
           $data = $this->initParams($data, $key);  //通过随机参数aes解密出来真正的参数
           $_REQUEST = $data;
       }
        header('content-type:text/html;charset=utf-8');

        $this->check_access_token();
    }

    #检查是否需要验证access_token
    private function check_access_token(){
        $params = $this->get_params();
        $controller_name = strtolower($this->getRequest()->controller);

        #是否需要登录
        $require_login = false;
        if ($controller_name == 'my') {
            $require_login = true;
        }

        if($require_login ){
            if(!isset($params['token']) && empty($params['token'])){
                $this->responseJson('10008');
            }

            $token = trim($params['token']);
            $user_model = new UserModel();
            $user_info = $user_model->getDataByUnionId($token);
            $this->uid = $user_info['uid'];

            if(empty($this->uid)){
                $this->responseJson('10008');
            }
        }
    }


    /**
     * 解密请求
     * @param void
     * @return mixed
     */
    protected function decryptRequest(){
        $params = self::I('params', '');
        $key = self::I('key', '');

        if(empty($params) || empty($key) ){
            $this->responseJson(10009);
        }

        $aes_key = self::initKey($key);
        $this->aes_key = $aes_key;

        $get_params = self::initParams($params, $aes_key);
        if( $this->_method === 'GET' )
            $_GET = $get_params;
        elseif( $this->_method === 'POST' )
            $_POST = $get_params;
        $_REQUEST = $get_params;
    }



    /**
     * 处理 key
     * @param string $key
     * @return mixed
     */
    private function initKey($key){
        $aes_key = Rsa::decrypt($key);
        if( empty($aes_key) )
            self::responseJson(10010);

        return $aes_key;
    }

    /**
     * 处理 params
     * @param string $params
     * @param string $key
     * @return mixed
     */
    private function initParams($params, $key){
        $json = Aes::decrypt($params, $key);
        if( empty($json) )
            self::responseJson(10011);

        $data = @json_decode($json, true);
        if( empty($data) )
            self::responseJson(10012);

        return $data;
    }

    #get params
    private function get_params(){
        return $_REQUEST;
    }


    function get_curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result_json = curl_exec($ch);
        curl_close($ch);
        return  json_decode($result_json, true);
    }
    function post_curl($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result_json = curl_exec($ch);
        curl_close($ch);
        return  json_decode($result_json, true);
    }


}
