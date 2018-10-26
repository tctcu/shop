<?php
class AdminUserController extends AdminController{

	function init(){
		parent::init();
	}

	#用户列表
	function indexAction() {
		$condition = array();
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$page_size = 20;
		$admin_user_model = new AdminUserModel();
		$show_list = $admin_user_model->getListData($page,$page_size,$condition);

		$this->_view->page = $page;
		$this->_view->show_list = $show_list;
		#分页处理
		$total_num = $admin_user_model->getListCount($condition);
		$pagination = $this->getPagination($total_num, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$this->_layout->meta_title = '后台用户列表';
	}

	#添加/编辑用户
	function createAction() {
		$uid = !empty($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$admin_user_model = new AdminUserModel();

		if($uid > 0){
			$info = $admin_user_model->getDataByUid($uid);
		}

		if($this->getRequest()->isPost()) {
			$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
			$mobile = isset($_REQUEST['mobile']) ? intval($_REQUEST['mobile']) : 0;

			if(empty($name) || empty($mobile)) {
				$this->set_flush_message('必填不能为空');
				$this->redirect('/admin/adminuser/create?uid='.$uid);
				return FALSE;
			}

			$data = array(
				'name' => $name,
				'mobile' => $mobile,
			);

			if(!empty($info['uid'])) {
				try{
					$admin_user_model->updateData($data, $info['uid']);
				}catch(Exception $e){
					$this->set_flush_message("修改后台用户失败");
					$this->redirect('/admin/adminuser/create?uid='.$uid);
					return FALSE;
				}
			} else {
				$password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
				if(empty($password)) {
					$this->set_flush_message('密码不能为空');
					$this->redirect('/admin/adminuser/create');
					return FALSE;
				}
				$salt = rand(1000,9999);
				$data['salt'] = $salt;
				$data['password'] = md5(md5($password).$salt);
				try{
					$admin_user_model->addData($data);
				}catch(Exception $e){
					$this->set_flush_message("添加后台用户失败");
					$this->redirect('/admin/adminuser/create');
					return FALSE;
				}
			}

			$this->set_flush_message("编辑/添加后台用户成功");
			$this->redirect('/admin/adminuser/index');
			return FALSE;
		}
		$this->_view->info = $info;
		$this->_layout->meta_title = '编辑/添加后台用户';
	}

	#权限管理
	function roleAction(){
		$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		if(empty($uid)){
			$this->set_flush_message('未选择用户');
			$this->redirect('/admin/adminuser/index');
			return False;
		}
		
		$admin_roles_model = new AdminRolesModel();
		$admin_user_model = new AdminUserModel();
		$user_info = $admin_user_model->getDataByUid($uid);
		if(empty($user_info)){
			$this->set_flush_message('用户不存在');
			$this->redirect('/admin/adminuser/index');
			return False;
		}
		if($this->getRequest()->isPost()){
			#删除原权限
			$admin_roles_model->deleteByUid($uid);
			$aids = !empty($_REQUEST['aids']) ? $_REQUEST['aids'] : array();
			foreach ($aids as $aid) {
				$add_array = array('uid' => $uid, 'access_id' => $aid);
				$admin_roles_model->addData($add_array);
			}
			$this->set_flush_message("修改后台用户权限成功");
			$this->redirect('/admin/adminuser/index/');
			return FALSE;
		}
		$access_array = $admin_roles_model->getAccessList();
		$roles_array = $admin_roles_model->getAllByUid($uid);

		$roles_list = $access_list = [];
		foreach ($access_array as $val) {
			$access_list[$val['id']] = $val['title'];
		}
		foreach ($roles_array as $val) {
			$roles_list[$val['access_id']] = 1;
		}

		$this->_view->access_list = $access_list;
		$this->_view->roles_list = $roles_list;
		$this->_view->user_info = $user_info;
		$this->_layout->meta_title = '修改后台用户权限';
	}

	#密码重置
	function resetAction(){
		$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		if(empty($uid)){
			$this->set_flush_message('未选择用户');
			$this->redirect('/admin/adminuser/index');
			return False;
		}

		$admin_user_model = new AdminUserModel();
		$info = $admin_user_model->getDataByUid($uid);
		if(empty($info['uid'])){
			$this->set_flush_message('后台用户不存在');
			$this->redirect('/admin/adminuser/index');
			return False;
		}
		$password = $uid.'123456';
		$salt = rand(1000,9999);
		$update = [
			'salt' => $salt,
			'password' => md5(md5($password).$salt)
		];
		$admin_user_model->updateData($update,$uid);
		$str = '密码重置为'.$password;
		$this->set_flush_message($str);
		$this->redirect('/admin/adminuser/index');
		return FALSE;
	}
	
}