<?php
#用户
class UserController extends ApiController
{

    function init()
    {
        parent::init();
    }

    #登录
    function loginAction(){
        $mobile = intval($_REQUEST['mobile']);
        $password = addslashes(htmlspecialchars(trim($_REQUEST['password'])));
        if (strlen($password)<>32) {
            $this->responseJson('10006', '密码不正确');
        }
        if($mobile && $password) {
            $user_model = new UserModel();
            $user_info = $user_model->getDataByMobile($mobile);

            if (empty($user_info)) {
                $this->responseJson('10006', '用户不存在');
            }

            if (md5($password . $user_info['salt']) <> $user_info['password']) {
                $this->responseJson('10006', '密码错误');
            }
            $data = array(
                'bind_mobile' => '2',
                'token' => $user_info['w_unionid'],
            );
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
        }
        $this->responseJson('10006');
    }

    #注册 关闭入口
    function registerAction(){
        $this->responseJson('10006', '请升级软件后注册');
        $mobile = isset($_REQUEST['mobile']) ? intval($_REQUEST['mobile']) : 0;
        $password = isset($_REQUEST['password']) ? addslashes(htmlspecialchars(trim($_REQUEST['password']))) : '';
        $device_type = intval($_REQUEST['device_type']);
        $device = trim($_REQUEST['device']);
        if (strlen($password)<>32) {
            $this->responseJson('10006', '密码不正确');
        }
        if($mobile && $password && $device_type && $device) {
            $user_model = new UserModel();
            $user_info = $user_model->getDataByMobile($mobile);
            if ($user_info) {
                $this->responseJson('10006', '用户已存在');
            }

            $salt = rand(1000, 9999);
            $insert = array(
                'mobile' => $mobile,
                'device' => $device,
                'device_type' => $device_type,
                'salt' => $salt,
                'password' => md5($password . $salt),
            );

            $user_model->addData($insert);
            $data = array(
                'mobile' => $insert['mobile'].'',
                'token' => $insert['w_unionid'],
            );
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
        }

        $this->responseJson('10006');
    }




    #获取授权签名 (废弃)
    function accreditSignAction(){
        $redirect_uri = "http://" . $_SERVER['HTTP_HOST'] . "/Alipay/callback";
        $model = new AlipayModel();
        $url = $model->oauth2code($redirect_uri);
        $data = [
            [
                'name' => '支付宝',
                'url' => $url
            ],
            [
                'name' => '微信',
                'url' => 'wechat://'
            ],
        ];
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #授权回调 (废弃)
    function zfbCallbackAction(){
        $auth_code = addslashes(htmlspecialchars(trim($_REQUEST['auth_code'])));
        if (empty($auth_code)) {
            $this->responseJson('10006');
        }

        $model = new AlipayModel();
        $token = $model->AlipaySystemOauthTokenRequest('authorization_code', $auth_code);
        if($token == '失败'){
            $this->responseJson('10007','授权失败');
        }
        $user_data = $model->AlipayUserInfoShareRequest($token['access_token']);
        if(empty($user_data['user_id'])){
            $this->responseJson('10007','授权失败');
        }
        #用户信息入表
        $info = [
            'z_gender' => $user_data['gender'],
            'z_is_certified' => $user_data['is_certified'],
            'z_user_status' => $user_data['user_status'],
            'z_user_type' => $user_data['user_type'],
            'z_is_student_certified' => $user_data['is_student_certified'],
            'z_nick_name' => $user_data['nick_name'],
            'z_city' => $user_data['city'],
            'z_province' => $user_data['province'],
            'z_avatar' => $user_data['avatar'],
            'z_user_id' => $user_data['user_id']
        ];
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
    }


    #微信授权回调登录
    function wxCallbackAction(){
        $auth_code = addslashes(htmlspecialchars(trim($_REQUEST['auth_code'])));
        $device_type = intval($_REQUEST['device_type']);
        $device = trim($_REQUEST['device']);

        if(empty($auth_code)){
            $this->responseJson('10007','授权失败');
        }
        $model = new WechatOpenModel();
        $token_info = $model->getAccessToken($auth_code);
        $user_data = $model->getUserInfo($token_info['access_token'],$token_info['openid']);
        $user_model = new UserModel();
        $user_info = $user_model->getDataByUnionid($user_data["unionid"]);
        if(empty($user_info)){ //注册
            $insert = [
                "w_openid" => $user_data["oOzFM08dsTrUSVkVvEErUYxVahX0"],
                "w_nickname" => $user_data["nickname"],
                "w_sex" => $user_data["sex"],
                "w_city" => $user_data["city"],
                "w_province" => $user_data["province"],
                "w_country" => $user_data["country"],
                "w_headimgurl" => $user_data["headimgurl"],
                "w_unionid" => $user_data["unionid"],
                "device_type" => $device_type,
                "device" => $device
            ];
            $user_model->addData($insert);
            $data = [
                'token' => $user_data['unionid'],
                'bind_mobile' => '1',
            ];
        } else { //登录
            $data = [
                'token' => $user_info['w_unionid'],
                'bind_mobile' => $user_info['mobile'] ? '2' : '1',
            ];
        }

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }
}