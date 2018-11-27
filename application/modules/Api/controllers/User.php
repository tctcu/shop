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
                'mobile' => $user_info['mobile'],
                'token' => $user_info['password'],
            );
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
        }
        $this->responseJson('10006');
    }

    #注册
    function registerAction(){
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
                'token' => $insert['password'],
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
    function callbackAction(){
        $auth_code = addslashes(htmlspecialchars(trim($_REQUEST['auth_code'])));
        if (empty($auth_code)) {
            $this->responseJson('10006');
        }

        $model = new AlipayModel();
        $token = $model->AlipaySystemOauthTokenRequest('authorization_code', $auth_code);
        if($token == '失败'){
            $this->responseJson('10007','授权失败');
        }
        $user_info = $model->AlipayUserInfoShareRequest($token['access_token']);
        if(empty($user_info['user_id'])){
            $this->responseJson('10007','授权失败');
        }
        #用户信息入表
        $info = [
            'z_gender' => $user_info['gender'],
            'z_is_certified' => $user_info['is_certified'],
            'z_user_status' => $user_info['user_status'],
            'z_user_type' => $user_info['user_type'],
            'z_is_student_certified' => $user_info['is_student_certified'],
            'z_nick_name' => $user_info['nick_name'],
            'z_city' => $user_info['city'],
            'z_province' => $user_info['province'],
            'z_avatar' => $user_info['avatar'],
            'z_user_id' => $user_info['user_id']
        ];
        $user_model = new UserModel();
        $user_data = $user_model->getDataByZUserId($user_info['user_id']);
        if($user_data){

        }
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
    }


}