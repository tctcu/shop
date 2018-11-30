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

        $user_data = $user_model->getDataByMobile($mobile);
        $update = [
            'mobile' => $mobile
        ];

        if($user_data){
            if(in_array($mobile,['15305634799','18217101927','17621372073','18110850336'])){ //处理历史数据
                $update['password'] = $user_data['password'];
                $update['salt'] = $user_data['salt'];
                $user_model->updateData(['mobile'=>''],$user_data['uid']);
            } else {
                $this->responseJson('10007','该手机号已被绑定：' . substr_replace($user_info['w_nickname'], '**', 2, 2));
            }
        }

        $user_model->updateData($update,$user_info['uid']);
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

            if (empty($user_info['mobile'])) {
                $this->responseJson('10006', '请先绑定手机号');
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