<?php
/*
 * 微信开放平台模型
 *
 * */

class WechatOpenModel extends WechatModel
{

    public function __construct($type=1){
        $wechat_config = Yaf_Registry::get("config")->get('wechat.open.'.$type);
        $this->appid = $wechat_config->appid;
        $this->secret = $wechat_config->secret;
    }


    #获取access_token
    public function getAccessToken($auth_code){
        if(empty($auth_code)){
            return false;
        }
        $request_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->secret."&code=".$auth_code."&grant_type=authorization_code";
        $result = $this->get_curl($request_url);
        return $result;
    }

    #获取用户信息 包含 unionid
    public function getUserInfo($access_token,$openid){
        if(empty($access_token) || empty($openid)){
            return false;
        }
        $request_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid";
        $result = $this->get_curl($request_url);
        return $result;
    }




}