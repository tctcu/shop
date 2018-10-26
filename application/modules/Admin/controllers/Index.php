<?php
class IndexController extends AdminController
{
    function init(){
        parent::init();
    }

    #后台主页
    public function indexAction(){
        $this->_layout->javascript_block = array(
            '/js/chart.js',
            '/js/jquery.timer.js');
        $this->_layout->meta_title = '主页';
    }


    #登陆
    public function loginAction(){
        $this->_layout->meta_title = '登录';
        $redirect_url = isset($_GET['redirect_url']) ? addslashes(htmlspecialchars(trim($_GET['redirect_url']))): '';
        $this->_view->redirect_url = $redirect_url;

        if($this->getRequest()->isPost()) {
            $mobile = intval($_POST['mobile']);
            $password = addslashes(htmlspecialchars(trim($_POST['password'])));

            if($mobile && $password) {
                $admin_user_model = new AdminUserModel();
                $user_info = $admin_user_model->getDataByMobile($mobile);

                if(empty($user_info)){
                    $this->set_flush_message('手机号不存在');
                    $this->redirect('/admin/index/login/');
                    exit;
                }

                if(md5(md5($password).$user_info['salt']) <> $user_info['password']){
                    $this->set_flush_message('密码不正确');
                    $this->redirect('/admin/index/login/');
                    exit;
                }

                $user_data_fileds = array('mobile' => '','uid'=> '','name' => '', 'status' => '', 'type' => 0);
                foreach ($user_data_fileds as $key => $value) {
                    $user_data_fileds[$key] = $user_info[$key];
                }

                $this->set_current_user($user_data_fileds);

                if(isset($_POST['redirect_url'])){
                    $this->redirect(trim($_POST['redirect_url']));
                }else{
                    $this->redirect('/admin/index/index');
                }
                exit;
            } else {
                $this->_view->message = "邮箱和密码不能为空!";
            }
        }
    }

    #注销
    public function logoutAction(){
        $this->set_current_user();
        $this->redirect('/admin/index/index');
        return FALSE;
    }

    #修改密码
    public function changePasswordAction(){
        $user = $this->get_current_user();
        if(empty($user['uid'])){
            $this->redirect('/admin/index/index');
        }
        if($this->getRequest()->isPost()) {
            $old_password = addslashes(htmlspecialchars(trim($_POST['old_password'])));
            $password = addslashes(htmlspecialchars(trim($_POST['password'])));

            if(empty($old_password) || empty($password)){
                $this->set_flush_message('新/老密码都不能为空');
                $this->redirect('/admin/index/changePassword/');
                exit;
            }
            $admin_user_model = new AdminUserModel();
            $info = $admin_user_model->getDataByUid($user['uid']);

            if(empty($info['uid'])){
                $this->set_flush_message('账号不存在');
                $this->redirect('/admin/index/login/');
                exit;
            }

            if(md5(md5($old_password).$info['salt']) <> $info['password']){
                $this->set_flush_message('原始密码不正确');
                $this->redirect('/admin/index/changePassword/');
                exit;
            }

            $update = [
                'password' => md5(md5($password).$info['salt'])
            ];
            $admin_user_model->updateData($update,$info['uid']);
            $this->set_flush_message('密码修改成功');
            $this->redirect('/admin/index/login/');
            exit;
        }
    }
}