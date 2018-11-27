<?php

class AlipayController extends Yaf_Controller_Abstract
{
    private $_log = true;//日志开关

    function init()
    {
        header('Content-type: text/html; charset=UTF-8');
    }

    #授权
    function indexAction()
    {
        $redirect_uri = "http://" . $_SERVER['HTTP_HOST'] . "/Alipay/callback";
        $model = new AlipayModel();
        $url = $model->oauth2code($redirect_uri);
        echo "<a style='font-size:50px' href='" . $url . "'>点击去授权</a>";
        return false;
    }

    #授权回调 获取token
    function callbackAction()
    {
        //app_id=2016032301002387 &scope=auth_user&auth_code=10e20498fe5d42f18427d893fc06WX59
        $this->log(json_encode($_REQUEST));
        $auth_code = $_REQUEST['auth_code'];
        if (empty($auth_code)) {
            return false;
        }
        $model = new AlipayModel();
        $token = $model->AlipaySystemOauthTokenRequest('authorization_code', $auth_code);
        $user_info = $model->AlipayUserInfoShareRequest($token['access_token']);
        echo '<pre>';
        print_r($user_info);

        echo "<a style='font-size:50px' href='ecommerce://'>点击返回app</a>";
        die;
        echo '<pre>';
        var_dump($token);
        return false;
    }


    #芝麻认证
    function zmrzAction()
    {
        //$biz_no = 'ZM201810253000000212100295954613';
        $model = new AlipayModel();

        if (empty($biz_no)) {
            $transaction_id = date("YmdHis") . rand(0000, 9999);//每次不一样 换取biz_no
            $identity_param = [
                "identity_type" => "CERT_INFO",
                "cert_type" => "IDENTITY_CARD",
                "cert_name" => "李金勇",
                "cert_no" => "36230219690111601X"
            ];
            $biz_no = $model->ZhimaCustomerCertificationInitializeRequest($transaction_id, $identity_param);
        }

        if(empty($biz_no)){
            echo '失败';
            die;
        }

        //获取认证url
        $redirect_uri = "http://" . $_SERVER['HTTP_HOST'] . "/Alipay/redirect";
        $request_url = $model->ZhimaCustomerCertificationCertifyRequest($biz_no, $redirect_uri);
        $request_url = "alipays://platformapi/startapp?appId=20000067&url=" . urlencode($request_url);

        echo "<a style='font-size:50px' href='" . $request_url . "'>点击去认证</a>";
        die;
    }


    #芝麻认证回调 查询认证结果
    function redirectAction()
    {
//        $biz_content = json_decode($_REQUEST['biz_content'], true);
//        $biz_no = $biz_content['biz_no'];
        $biz_no = 'ZM201811073000000696900378949926';

        $model = new AlipayModel();
        if (empty($biz_no)) {
            echo '非法请求';
        }
        $result = $model->ZhimaCustomerCertificationQueryRequest($biz_no);

        $this->log($biz_no);
        if ($result['passed'] == 'true') {
            echo '<h1>认证成功</h1>';
        } else {
            echo '<h1>认证失败</h1>';
            echo '<h2>' . $result['failed_reason'] . '</h2>';
        }
        return false;
    }

    #查询蚁盾（ge根据手机号查询）
    function riskAction(){
        $mobile = 17621372073;

        $model = new AlipayModel();
        if (empty($mobile)) {
            echo '非法请求';
        }
        $result = $model->mobileRisk($mobile);
        die;
    }

    private function log($content = '')
    {
        if ($this->_log) {
            $fp = fopen('/tmp/alipay.log', 'a');
            if (!$fp) {
                return;
            }
            fwrite($fp, $content . "\n");
            fclose($fp);
        }
    }

}