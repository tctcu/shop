<?php
class StatController extends AdminController
{

	function init()
	{
		parent::init();
	}

	function indexAction(){
		$condition = array();
		$condition['item_title'] = isset($_REQUEST['item_title']) ? trim($_REQUEST['item_title']) : '';
		$condition['uid'] = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$condition['tk_status'] = isset($_REQUEST['tk_status']) ? intval($_REQUEST['tk_status']) : 0;
		$condition['trade_id'] = isset($_REQUEST['trade_id']) ? intval($_REQUEST['trade_id']) : '';
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$page_size = 20;
		$tb_order_model = new TbOrderModel();
		$show_list = $tb_order_model->getListData($page,$page_size,$condition);

		$this->_view->show_list = $show_list;
		#分页处理
		$total_num = $tb_order_model->getListCount($condition);
		$pagination = $this->getPagination($total_num, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);
		$this->_view->params = $condition;

		$this->_layout->meta_title = '订单列表';
	}

}