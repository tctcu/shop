<?php
abstract class AdminController extends Yaf_Controller_Abstract{

	protected $_layout;
	protected $base_url = '';

	public function init(){
		$this->_layout = Yaf_Registry::get('layout');
		$user = $this->get_current_user();
		$user_is_login = $user ? true : false;
		$this->_layout->user = $user;
		$this->_layout->user_is_logined = $user_is_login;
		$this->_layout->flush_message = $this->get_flush_message();

		$module = strtolower($this->getRequest()->module);
		$controller = strtolower($this->getRequest()->controller);
		$action = strtolower($this->getRequest()->action);

		$this->base_url = '/'.$module.'/'.$controller.'/'.$action.'/';

		if($controller <> 'index' || $action == 'changepassword'){ //修改密码必须登录
			if(empty($user_is_login)){
				$this->set_flush_message('必须登录后才能浏览内容');
				if (isset($_SERVER['REQUEST_URI'])) {
					$url = '/admin/index/login/?redirect_url=' . urlencode($_SERVER['REQUEST_URI']);
				} else {
					$url = '/admin/index/login/';
				}
				$this->redirect($url);
				return false;
			}

			if($user['type'] < 1 || $user['status']<>1 ){
				$this->set_flush_message('登录账号异常');
				$this->redirect('/admin/index/index');
				exit;
			}

			if($action <> 'changepassword') { //修改密码的权限不需要验证权限
				$admin_roles_model = new AdminRolesModel();
				$check_access = $admin_roles_model->checkAccess($user['uid'], $module, $controller, $action);
				if (!$check_access) {
					$this->set_flush_message('没有权限');
					$this->redirect('/admin/index/index');
					exit;
				}
			}

			$admin_menu_model = new AdminMenuModel;
			$this->_view->admin_menus = $admin_menu_model->getMenu($controller);
		}

		$this->_view->user_is_logined = $user_is_login;
		$this->_view->current_module = $module;
		$this->_view->current_controller = $controller;
		$this->_view->current_action = $action;
	}


	#设置用户到session
	function set_current_user($user = array()){
		$session_user_key = Yaf_Registry::get('config')->get('product.session_user');
		Yaf_Session::getInstance()->set($session_user_key,$user);
	}

	#获得当前用户
	function get_current_user(){
		return Yaf_Session::getInstance()->get(Yaf_Registry::get('config')->get('product.session_user'));
	}

	#判断用户是否登录
	function user_is_logined(){
		return $this->get_current_user() ? true : false;
	}

	#设置信息
	function set_flush_message($message){
		Yaf_Session::getInstance()->set('flush_message',$message);
	}

	#提取信息
	function get_flush_message(){
		$message = Yaf_Session::getInstance()->get('flush_message');
		if(!empty($message)){
			Yaf_Session::getInstance()->del('flush_message');
			return $message;
		}
		return '';
	}


	function referer(){
		$temp = 'HTTP_' . strtoupper(str_replace('-', '_', 'REFERER'));
		if (!empty($_SERVER[$temp])) return $_SERVER[$temp];

		if (function_exists('apache_request_headers'))
		{
			$headers = apache_request_headers();
			if (!empty($headers['REFERER'])) return $headers['REFERER'];
		}

		return false;
	}


	#获取分页参数
	public function getPagination($total_num = 0, $page = 1, $page_size = 20){
		$page_num = ceil($total_num / $page_size);
		$pagination = array(
			"record_count" => $total_num,
			"page_count" => $page_num,
			"first" => 1,
			"last" => $page_num,
			"next" => min($page_num, $page + 1),
			"prev" => max(1, $page - 1),
			"current" => $page,
			"page_size" => $page_size,
			"page_base" => 1,
		);
		return $pagination;
	}
}