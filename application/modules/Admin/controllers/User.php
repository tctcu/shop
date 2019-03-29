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
		$condition['mobile'] = isset($_REQUEST['mobile']) ? intval($_REQUEST['mobile']) : 0;
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

	#提现审核
	function payAction(){
		$condition = array();
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$condition['uid'] = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;

		$condition['type'] = 2;//类型 1-返利发放 2-提现申请 3-提现到账 4-提现失败
		$condition['pay_type'] = [1,2];//提现方式 1-支付宝 2-微信
		$page_size = 20;
		$account_record_model = new AccountRecordModel();
		$show_list = $account_record_model->getListData($page,$page_size,$condition);

		$this->_view->page = $page;
		$this->_view->show_list = $show_list;
		#分页处理
		$total_num = $account_record_model->getListCount($condition);
		$pagination = $this->getPagination($total_num, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$this->_layout->meta_title = '提现审核';
	}
}