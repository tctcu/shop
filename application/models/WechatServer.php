<?php
/*
 * 微信服务号模型
 *
 * */

class WechatServerModel extends WechatModel
{

    public function __construct($type=1){

        #1-皮兔皮
        switch($type){
            case 1:
                $this->token = 'allfreep2pserver';
                $this->appid = 'wx3a09c92112169321';
                $this->secret = '61425a1bcfdfb347ad9beba5b2e91f3d';
                $this->enckey = 'tzmfc0q8wHwLd7x8GDcoZpQd9vGWe7RJnCrENSwovIe';
                break;
        }
    }


    #获取access_token
    public function getAccessToken(){
        $request_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
        $result = $this->get_curl($request_url);
        return $result;
    }

    #获取用户信息
    public function getUserInfo($access_token,$openid){
        $request_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $result = $this->get_curl($request_url);
        return $result;
    }






    //网页授权
    #用户同意授权，获取code
    public function code($redirect_uri){
        $redirect_uri = urlencode($redirect_uri);
        $request_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=0908#wechat_redirect";
        header("Location:$request_url");exit;
    }

    #通过code换取网页授权access_token
    public function authorization_code($code){
        $request_url = "https://api.weixin.qq.com/sns/oauth2/access_token";
        $request_data['appid'] = $this->appid;
        $request_data['secret'] =  $this->secret;
        $request_data['code'] = $code;
        $request_data['grant_type'] = 'authorization_code';
        $result = $this->curl($request_url,$request_data);
        return $result;
    }

    #刷新access_token
    public function refresh_token($refresh_token){
        $request_url = "https://api.weixin.qq.com/sns/oauth2/refresh_token";
        $request_data['appid'] = $this->appid;
        $request_data['grant_type'] = 'refresh_token';
        $request_data['refresh_token'] = $refresh_token;
        $result = $this->curl($request_url,$request_data);
        return $result;
    }

    #拉取用户信息
    public function snsapi_userinfo($access_token,$openid){
        $request_url = "https://api.weixin.qq.com/sns/userinfo";
        $request_data['access_token'] = $access_token;
        $request_data['openid'] = $openid;
        $request_data['lang'] = 'zh_CN';
        $result = $this->curl($request_url,$request_data);
        return $result;
    }

    #检验access_token
    public function check_access_token($access_token,$openid){
        $request_url = "https://api.weixin.qq.com/sns/auth";
        $request_data['access_token'] = $access_token;
        $request_data['openid'] = $openid;
        $result = $this->curl($request_url,$request_data);
        return $result;
    }



    #微信企业付款
    private static $wechat_appid = 'wx3a09c92112169321';
    private static $wechat_merchantid = '1430062202';
    private static $trans_appkey = 'f39ff2c80103f02b7815a4f4db8a9b60';		// 商户平台中设置的交易密钥
    private static $wechat_apiurl = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    function transfers($req_param){
        #支付必须参数
        $param = array(
            'nonce_str',//自定义字符串
            'partner_trade_no',//订单号
            'openid',//openid
            're_user_name',//用户名
            'amount',//金额
        );

        if(ksort(array_keys($req_param)) != ksort($param)){
            return false;
        }

        #补充请求参数
        $req_param['mch_appid'] = self::$wechat_appid;
        $req_param['mchid'] = self::$wechat_merchantid;
        $req_param['spbill_create_ip'] = $_SERVER['SERVER_ADDR'];
        $req_param['desc'] = '支付';
        $req_param['check_name'] = 'FORCE_CHECK';

        ksort($req_param);

        $sign_str = '';
        foreach ($req_param as $key => $value){
            $sign_str .= $key . '=' . $value . '&';
        }
        $sign_str .= 'key=' . self::$trans_appkey;
        $req_param['sign'] = strtoupper(md5($sign_str));

        $req_xml = $this->array2Xml($req_param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, self::$wechat_apiurl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, dirname(__FILE__) . '/../library/Wechat/apiclient_cert.pem');
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, dirname(__FILE__) . '/../library//Wechat/apiclient_key.pem');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req_xml);
        $resp_xml = curl_exec($ch);
        curl_close($ch);

        if($resp_xml) {
            $resp_ary = $this->xml2Array($resp_xml);
            return $resp_ary;
        }
        return false;
    }

    #微信发红包
    function sendRedPack($req_param){
        $wechat_apiurl = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        #支付必须参数
        $param = array(
            'nonce_str',//自定义字符串
            'mch_billno',//订单号
            're_openid',//openid
            'total_amount',//金额
            'total_num',//人数
            'wishing',//祝福语
            'act_name',//活动名称
            'remark',//备注
        );

        if(ksort(array_keys($req_param)) != ksort($param)){
            return false;
        }

        #补充请求参数
        $req_param['wxappid'] = self::$wechat_appid;
        $req_param['mch_id'] = self::$wechat_merchantid;
        $req_param['client_ip'] = $_SERVER['SERVER_ADDR'];
        $req_param['send_name'] = '全民免费';

        ksort($req_param);

        $sign_str = '';
        foreach ($req_param as $key => $value){
            $sign_str .= $key . '=' . $value . '&';
        }
        $sign_str .= 'key=' . self::$trans_appkey;
        $req_param['sign'] = strtoupper(md5($sign_str));

        $req_xml = $this->array2Xml($req_param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $wechat_apiurl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, dirname(__FILE__) . '/../library/Wechat/apiclient_cert.pem');
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, dirname(__FILE__) . '/../library//Wechat/apiclient_key.pem');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req_xml);
        $resp_xml = curl_exec($ch);
        curl_close($ch);

        if($resp_xml) {
            $resp_ary = $this->xml2Array($resp_xml);
            return $resp_ary;
        }
        return false;
    }

    #微信转账数组转化为xml格式
    private function array2Xml($aryParam){
        $xml = "<xml>";
        foreach ($aryParam as $key => $val){
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    #微信转账xml转化为数组
    private function xml2Array($xml){
        #禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    #错误码
    function error($err_code){
        // 根据特定错误码进行提示
        $retCode = -1;
        switch ($err_code){
            case 'OPENID_ERROR':
                $alertMsg = '微信提现需要重新绑定微信服务号才可提现！';
                $retCode = -2;
                break;
            case 'NOTENOUGH':
                $alertMsg = '请联系客服人员,企业账户余额不足!';
                break;
            case 'NAME_MISMATCH':
                $alertMsg = '请确认提款姓名和微信绑定的银行卡姓名一致!';
                break;
            case 'SYSTEMERROR':
                $alertMsg = '微信服务器繁忙,请稍后再试!';
                break;
            case 'FREQ_LIMIT':
                $alertMsg = '您操作过于频繁，请稍后再试!';
                break;
            case 'SENDNUM_LIMIT':
                $alertMsg = '今日提现超过限制!';
                break;
            default :
                $alertMsg = '提现异常,请联系客服人员.错误码：' . $err_code;
                break;
        }

        $return['return_code'] = $retCode;
        $return['return_msg'] = $alertMsg;
        return $return;

    }


}