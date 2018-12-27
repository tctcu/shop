<?php
/**
 * 语雀
 */

class YuQueModel{
    private $session = '';
    private $site_id = '';
    private $adzone_id = '';


    public function __construct($type = 1){
        $yuque_config = Yaf_Registry::get("config")->get('taobao.account.'.$type);

        $this->session = $yuque_config->session;
        $this->site_id = $yuque_config->site_id;
        $this->adzone_id = $yuque_config->adzone_id;
    }

    private function curl($url,$data){
        $headers = array(
            "Content-type: application/json"
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时
        if(0 === strpos(strtolower($url), 'https')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);//从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result_json = curl_exec($ch);//CURLOPT_RETURNTRANSFER 不设置  curl_exec返回TRUE 设置  curl_exec返回json(此处) 失败都返回FALSE
        curl_close($ch);

        return  json_decode($result_json, true);
    }

    #淘口令换淘宝ID
    function tpwdConvert($condition = array()){
        $password_content = isset($condition['password_content']) ? trim($condition['password_content']) : '';//淘口令文案
        if(empty($password_content)){
            return array();
        }

        $resp = [
            'session' => $this->session,
            'password_content' => $password_content
        ];

        $url = 'http://gateway.kouss.com/tbpub/tpwdConvert';
        $resp = $this->curl($url,$resp);

        if(isset($resp['data']['num_iid']) && !empty($resp['data']['num_iid'])){
            $retData = $resp['data']['num_iid'];
        } else {
            $retData = array();
        }

        return $retData;
    }

    #转换高佣
    function privilegeGet($condition = array()){
        $item_id = isset($condition['item_id']) ? intval($condition['item_id']) : '';//淘ID
        if(empty($item_id)){
            return array();
        }

        $resp = [//目前账号没有高佣 用个人账号转高佣
            'session' => $this->session,
            'adzone_id' => $this->adzone_id,
            'site_id' => $this->site_id,
            'item_id' => $item_id
        ];

        $url = 'http://gateway.kouss.com/tbpub/privilegeGet';
        $resp = $this->curl($url,$resp);

        if(isset($resp['result']['data']) && !empty($resp['result']['data'])){
            $retData = $resp['result']['data'];
        } else {
            $retData = array();
        }

        return $retData;
    }


}