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

        if($mobile && $password) {
            $user_model = new UserModel();
            $user_info = $user_model->getDataByMobile($mobile);

            if (empty($user_info)) {
                $this->responseJson('10006', '用户不存在');
            }

            if (md5(md5($password) . $user_info['salt']) <> $user_info['password']) {
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
                'password' => md5(md5($password) . $salt),
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

}