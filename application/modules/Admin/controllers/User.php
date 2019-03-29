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
		$this->_view->params = $condition;

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
		$this->_view->params = $condition;

		$this->_layout->meta_title = '提现审核';
	}

	#发放提现
	function grantAction(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$action = isset($_REQUEST['action']) ? intval($_REQUEST['action']) : 0;


		$account_record_model = new AccountRecordModel();
		$account_record_data = $account_record_model->getDataById($id);
		if(empty($uid) || $account_record_data['uid'] <> $uid){
			$this->set_flush_message("非法请求");
			$this->redirect('/admin/user/pay/');
			return FALSE;
		}

		if($action<>1){//驳回
			//提现记录更新
			$account_record_model->updateData([
				'type' => 4,//类型 3-提现到账 4-提现失败
			],$id);
			//补回金额
			$this->refund($uid,$account_record_data['money']);

			$this->set_flush_message("驳回");
			$this->redirect('/admin/user/pay/');
			return FALSE;
		}
		if($account_record_data['pay_type'] == 1){//支付宝
			$res = $this->zfb($uid,$account_record_data['money']);
			if( $res === false){
				$this->set_flush_message("提现申请信息有误");
				$this->redirect('/admin/user/pay/?uid='.$uid);
				return FALSE;
			}
			//提现记录更新
			$account_record_model->updateData([
				'type' => $res['type'] == 2 ? 3 : 4,//类型 3-提现到账 4-提现失败
				'pay_id' => $res['pay_id'],
			],$id);

			if($res['type'] <> 2){ //支付失败
				//补回金额
				$this->refund($uid,$account_record_data['money']);

				$this->set_flush_message($res['msg']);
				$this->redirect('/admin/user/pay/');
				return FALSE;
			}

		} else {
			$this->set_flush_message("只支持支付宝提现");
			$this->redirect('/admin/user/pay/');
			return FALSE;
		}

		$this->set_flush_message("提现成功");
		$this->redirect('/admin/user/pay/');
		return FALSE;
	}

	//退还可提金额
	private function refund($uid,$money){
		if($money < 1){
			return false;
		}
		$user_model = new UserModel();
		$user_info = $user_model->getDataByUid($uid);
		$user_model->updateData(['use'=>$user_info['use']+$money],$uid);
		return true;
	}

	//支付宝提现
	private function zfb($uid,$money){
		if(empty($uid) || $money < 1) {
			return false;
		}
		$user_model = new UserModel();
		$user_info = $user_model->getDataByUid($uid);

		if(empty($user_info['account']) || empty($user_info['name']) ||  $user_info['total'] < $money ){
			return false;
		}

		$out_biz_no = $uid.time();
		$model = new AlipayModel();
		$res = $model->AlipayFundTransToaccountTransferRequest($out_biz_no, $user_info['account'], $user_info['name'], $money);

		$alipay_extract_model = new AlipayExtractModel();
		$pay_id = $alipay_extract_model->addData([
			'uid' => $uid,
			'type' => $res['type'],
			'msg' => $res['msg'],
			'code' => $res['code'],
			'order_id' => $res['order_id'],
			'pay_date' => $res['pay_date'],
			'name' => $user_info['name'],
			'account' => $user_info['account']
		]);
		//支付成功更新用户绑定
		if ($res['type'] == 2 && $user_info['z_bind']<>1) {
			$user_model->updateData(['z_bind'=>1],$uid);
		}

		$res['pay_id'] = $pay_id;
		return $res;
	}

	//微信提现
	private function wechat(){

	}

}