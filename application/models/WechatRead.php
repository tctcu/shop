<?php
/*
 * 微信订阅号模型
 *
 * */

class WechatReadModel extends WechatModel
{

    public function __construct($type=1){

        #1-小鱼轻松赚 2-赚的容易
        switch($type){
            case 1:
                $this->token = 'allfree';
                $this->appid = 'wx11ef5598d6dbec05';
                $this->secret = '9fdc2190aa2f0403698d64554a054e96';
                $this->enckey = 'u0z9SbnpUxKF7pvQNk5LyKs6YFckTthXxBUjxx2PRLb';
                break;
            case 2:
                $this->token = 'allfreep2p';
                $this->appid = 'wx87a3bff239c2b4c6';
                $this->secret = 'b9060cf1335fd5db9ffc0e12bc1cec2e';
                $this->enckey = 'T35FaqLlB31jTfXAHS7MOUlMtq9qsQIvamsTdiiK9uv';
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


}