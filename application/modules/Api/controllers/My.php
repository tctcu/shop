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
        $data = [
            'mobile' => $mobile ? substr_replace($mobile, '****', 3, 4) : '',
            'bind_mobile' => '2',
            'token' => $user_info['w_unionid'],
            'headimgurl' => $user_info['w_headimgurl'],
            'nickname' => $user_info['w_nickname']
        ];
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
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
            $data = [
                'mobile' => $user_info['mobile'] ? substr_replace($user_info['mobile'], '****', 3, 4) : '',
                'bind_mobile' => '2',
                'token' => $user_info['w_unionid'],
                'headimgurl' => $user_info['w_headimgurl'],
                'nickname' => $user_info['w_nickname']
            ];
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
        }

        $this->responseJson('10006');
    }

    #跟单获取连接
    function getUserUrlAction(){
        $itemid = intval($_REQUEST['itemid']);

        $uid = $this->uid;

        $error = true;
        if($uid){
            $user_model = new UserModel();
            $user_info = $user_model->getDataByUid($uid);
            if($user_info){
                $error = false;
            }
        }
        if($error){
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
        }
        $user_pid_model = new UserPidModel();
        $pid_info = $user_pid_model->getDataByUid($uid);

        if(empty($pid_info['site_id']) || empty($pid_info['adzone_id'])){
            //关联上一个pid
            $user_pid_model->bindUser($uid);
            $pid_info = $user_pid_model->getDataByUid($uid);
        }

        $type = ConfigModel::MEMBER[$pid_info['memberid_id']];

        #获取库信息
        $tb_model = new TbModel();
        $tb_info = $tb_model->getDataByItemId($itemid);
        $taobao_account = Yaf_Registry::get("config")->get('taobao.account.' . $type);
        $taobao_model = new TaobaoModel($type);
        if($tb_info['activityid']){//好单库转高佣
            $url = "http://v2.api.haodanku.com/ratesurl";
            $request_data['apikey'] = 'allfree';
            $request_data['itemid'] = $itemid;
            $request_data['pid'] = 'mm_'.$pid_info['memberid_id'].'_'.$pid_info['site_id'].'_'.$pid_info['adzone_id'];
            $request_data['activityid'] = $tb_info['activityid'];
            $request_data['tb_name'] = $taobao_account->name;
            $url_info = $this->post_curl($url,$request_data);
            $url_info = $url_info['data'];
            $tpwd = [
                'text' => $tb_info['itemshorttitle'],
                'logo' => $tb_info['itempic']
            ];
        }

        if (empty($url_info['coupon_click_url'])) {//其他方式 语雀
            $condition = [
                'item_id' => $itemid,
                'session' => $taobao_account->session,
                'site_id' => $pid_info['site_id'],
                'adzone_id' => $pid_info['adzone_id']
            ];

            $yuque_model = new YuQueModel($type);
            $url_info = $yuque_model->privilegeGet($condition);

            $condition = [
                'item_id' => $itemid
            ];
            $item_info = $taobao_model->TbkItemInfoGetRequest($condition);
            $tpwd = [
                'text' => $item_info['title'],
                'logo' => $item_info['pict_url']
            ];
        }

        $data = [];
        if ($url_info['coupon_click_url']) {
            $tpwd['url'] = $url_info['coupon_click_url'];
            $res = $taobao_model->TbkTpwdCreateRequest($tpwd);
            $data = [
                'item_id' => $itemid.'',
                'url' => $url_info['coupon_click_url'],
                'tpwd' => $res
            ];
        }
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #订单
    function orderAction(){
        $min_id = isset($_REQUEST['min_id']) ? intval($_REQUEST['min_id']) : '0';
        $uid = $this->uid;

        $error = true;
        if($uid){
            $user_model = new UserModel();
            $user_info = $user_model->getDataByUid($uid);
            if($user_info){
                $error = false;
            }
        }
        if($error){
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
        }

        $user_pid_model = new UserPidModel();
        $pid_info = $user_pid_model->getDataByUid($uid);
        $data = [];

        if($pid_info['site_id'] && $pid_info['adzone_id']){
            $condition = [
                'min_id' => $min_id,
                'site_id' => $pid_info['site_id'],
                'adzone_id' => $pid_info['adzone_id']
            ];
            $tb_order_model = new TbOrderModel();
            $order_info = $tb_order_model->getList(20,$condition);
            $data = $tb_order_model->makeOrder($order_info);
        }

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #账户
    function accountAction(){
        $uid = $this->uid;
        $error = true;
        if($uid){
            $user_model = new UserModel();
            $user_info = $user_model->getDataByUid($uid);
            if($user_info){
                $error = false;
            }
        }
        if($error){
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
        }
        $today = $wait = '0.00';
        $user_pid_model = new UserPidModel();
        $pid_info = $user_pid_model->getDataByUid($uid);
        if($pid_info['site_id'] && $pid_info['adzone_id']){
            $tb_order_model = new TbOrderModel();
            $wait = $tb_order_model->getWaitByPid($pid_info['site_id'], $pid_info['adzone_id']);
            $today = $tb_order_model->getTodayByPid($pid_info['site_id'], $pid_info['adzone_id']);
        }

        $data = [
            'use' => !empty($user_info['use']) ? $user_info['use'] : '0.00',
            'today' => $today,
            'wait' => $wait,
            'total' => !empty($user_info['total']) ? $user_info['total'] : '0.00',
        ];

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #提现绑定信息
    function bindInfoAction(){
        $uid = $this->uid;

        $user_model = new UserModel();
        $user_info = $user_model->getDataByUid($uid);

        $data = [
            'z_status' => '1',//支付宝提现 1-开启 0-关闭
            'z_bind' => $user_info['z_bind'],//支付宝绑定 1-是 0-否
            'z_name' => $user_info['z_name'],
            'z_account' => $user_info['z_account'],
            'z_extract_level' => $user_info['z_bind'] == 0 ? [1,10,30] : [10,30,100],
            'w_status' => '0',//微信提现 1-开启 0-关闭
            'w_bind' => !empty($user_info['s_openid']) ? '1' : '0',//微信绑定 1-是 0-否
            'w_qr_code' => CommonModel::IMAGE_URL.CommonModel::BIND_WE_CHAT,
            'w_extract_level' => empty($user_info['s_openid']) ? [1,10,30] : [10,30,100],
        ];

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }


    #微信提现
    function wxExtractAction(){
        $uid = $this->uid;
        $money = intval($_REQUEST['money']);

        if(!in_array($money,[1,10,30,100])){
            $this->responseJson('10008', '提现金额有误');
        }

        $user_model = new UserModel();
        $user_info = $user_model->getDataByUid($uid);
        if(empty(['s_openid'])){
            $this->responseJson('10005', '请先绑定提现微信');
        }

        if($user_info['w_bind']){//已绑定
            if($money == 1){// 绑定之后不能提1元
                $this->responseJson('10008', '提现金额有误');
            }
        }

        $balance = $user_info['use'] - $money;
        if($balance < 0){
            $this->responseJson('10009', '可用余额不足');
        }

        $update_user = [
            'use' => $balance,
        ];
        $type = 2;//2-提现申请 3-提现到账
        $pay_id = 0;
        if($money == 1) {//未绑定 1元立即到账
            //TODO:: 微信支付1元
            $res['type']  = 0;
            if ($res['type'] == 2) {// 支付成功
                $update_user['w_bind'] = 1;
                $type = 3;
            } else {
                $this->responseJson('10011', '微信提现敬请期待');
            }
        }
        //更新用户余额等
        $user_model->updateData($update_user,$uid);
        //提现记录
        $account_record_model = new AccountRecordModel();
        $account_record_model->addData([
            'uid' => $uid,
            'type' => $type,
            'pay_type' => 2,//1-支付宝 2-微信
            'pay_id' => $pay_id,
            'before' => $user_info['use'],
            'money' => $money,
            'balance' => $balance,
        ]);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
    }


    #支付宝提现
    function zfbExtractAction(){
        $uid = $this->uid;
        $money = intval($_REQUEST['money']);
        $name = trim($_REQUEST['name']);
        $account = trim($_REQUEST['account']);

        if(empty($name) || empty($account) || $money <= 0){
            $this->responseJson('10008', '非法提现');
        }
        if(!in_array($money,[1,10,30,100])){
            $this->responseJson('10008', '提现金额有误');
        }

        $user_model = new UserModel();
        if(!(preg_match('/^[\x{4e00}-\x{9fa5}]{2,10}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,20}$/isu',$name))){
            $this->responseJson('10008', '请提供真实的支付宝实名信息');
        }

        if(!($user_model->is_mobile_phone($account) || $user_model->is_email($account))){
            $this->responseJson('10008', '支付宝账号非邮箱或手机号');
        }

        $user_info = $user_model->getDataByUid($uid);
        $update_user = [];
        if($user_info['z_bind']){//已绑定
            if($account <> $user_info['z_account'] || $name <> $user_info['z_name']){
                $this->responseJson('10007', '提现实名信息不正确');
            }
            if($money == 1){// 绑定之后不能提1元
                $this->responseJson('10008', '提现金额有误');
            }
        } else {// 未绑定 更新用户提现信息
            $update_user = [
                'z_name' => $name,
                'z_account' => $account,
            ];
        }

        $balance = $user_info['use'] - $money;
        if($balance < 0){
            $this->responseJson('10009', '可用余额不足');
        }
        $update_user['use'] = $balance;
        $type = 2;//2-提现申请 3-提现到账
        $pay_id = 0;
        if($money == 1) {//1元立即到账
            $out_biz_no = $uid.time();

            $model = new AlipayModel();
            $res = $model->AlipayFundTransToaccountTransferRequest($out_biz_no, $account, $name, $money);

            $alipay_extract_model = new AlipayExtractModel();
            $pay_id = $alipay_extract_model->addData([
                'uid' => $uid,
                'type' => $res['type'],
                'msg' => $res['msg'],
                'code' => $res['code'],
                'order_id' => $res['order_id'],
                'pay_date' => $res['pay_date'],
                'name' => $name,
                'account' => $account
            ]);

            if ($res['type'] == 2) {// 支付成功
                $update_user['z_bind'] = 1;
                $type = 3;
            } else {
                unset($update_user['use']);
                $user_model->updateData($update_user,$uid);//记录上次输入的用户信息
                $this->responseJson('10011', '提现失败,'.$res['msg']);
            }
        }
        //更新用户余额等
        $user_model->updateData($update_user,$uid);
        //提现记录
        $account_record_model = new AccountRecordModel();
        $account_record_model->addData([
            'uid' => $uid,
            'type' => $type,
            'pay_type' => 1,//1-支付宝 2-微信
            'pay_id' => $pay_id,
            'before' => $user_info['use'],
            'money' => $money,
            'balance' => $balance,
        ]);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
    }

    #资金记录
    function accountRecordAction(){
        $uid = $this->uid;
        $min_id = intval($_REQUEST['min_id']) ? intval($_REQUEST['min_id']) : 0;

        $account_record_model = new AccountRecordModel();
        $account_list = $account_record_model->getList(20,[
            'uid' => $uid,
            'min_id' => $min_id
        ]);
        $data = [];
        foreach($account_list as $val){
            if(empty($data['min_id']) || $val['id'] < $data['min_id']){
                $data['min_id'] = $val['id'];
            }
            $data['list'][] = $account_record_model->makeAccountRecord($val);
        }
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

}