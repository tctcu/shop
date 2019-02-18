<?php
class StatController extends AdminController
{

	function init()
	{
		parent::init();
	}

	function indexAction(){
		$condition = array();
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

		$this->_layout->meta_title = '订单列表';
	}

}