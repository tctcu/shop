<?php
#个人 登录访问
class MyController extends ApiController
{

    function init()
    {
        parent::init();
    }

    #绑定手机号
    function bindMobileAction(){
        $token = addslashes(htmlspecialchars(trim($_REQUEST['token'])));
        $mobile = intval($_REQUEST['mobile']);
        $user_model = new UserModel();
        $user_info = $user_model->getDataByUnionId($token);
        if(empty($user_info)){
            $this->responseJson('10007','重新登录');
        }
        $user_model->updateData(['mobile' => $mobile],$user_info['uid']);
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
    }

    #找回密码
    function findPasswordAction(){
        $token = isset($_REQUEST['token']) ? addslashes(htmlspecialchars(trim($_REQUEST['token']))) : '';
        $password = isset($_REQUEST['password']) ? addslashes(htmlspecialchars(trim($_REQUEST['password']))) : '';

        if (strlen($password)<>32) {
            $this->responseJson('10006', '密码不正确');
        }
        if($token && $password) {
            $user_model = new UserModel();
            $user_info = $user_model->getDataByUnionId($token);
            if (empty($user_info)) {
                $this->responseJson('10006', '用户不存在');
            }

            $salt = rand(1000, 9999);
            $update = array(
                'salt' => $salt,
                'password' => md5($password . $salt),
            );

            $user_model->updateData($update,$user_info['uid']);
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
        }

        $this->responseJson('10006');
    }

}