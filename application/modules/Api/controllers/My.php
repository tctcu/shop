<?php
#个人 登录访问
class MyController extends ApiController
{

    function init()
    {
        parent::init();
    }

    #绑定手机号
    function bindMobile(){
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

}