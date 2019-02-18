<?php
class UserController extends AdminController
{

	function init()
	{
		parent::init();
	}
	#用户列表
	function indexAction(){
		$condition = array();
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$condition['uid'] = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$page_size = 20;
		$admin_user_model = new UserModel();
		$show_list = $admin_user_model->getListData($page,$page_size,$condition);

		$this->_view->page = $page;
		$this->_view->show_list = $show_list;
		#分页处理
		$total_num = $admin_user_model->getListCount($condition);
		$pagination = $this->getPagination($total_num, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$this->_layout->meta_title = '用户列表';
	}
}