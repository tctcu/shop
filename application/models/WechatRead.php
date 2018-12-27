<?php
/*
 * 微信订阅号模型
 *
 * */

class WechatReadModel extends WechatModel
{

    public function __construct($type=1){
        $wechat_config = Yaf_Registry::get("config")->get('wechat.read.'.$type);

        $this->token = $wechat_config->token;
        $this->appid = $wechat_config->appid;
        $this->secret = $wechat_config->secret;
        $this->enckey = $wechat_config->enckey;
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


}