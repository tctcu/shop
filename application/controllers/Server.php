<?php
/**
 * 微信服务号
 *
 * 微信菜单json：

{
"button":
[
{
"type": "view",
"name": "活动列表",
"url": "http://p2p.allfree.cc/wap/activity/index",
"sub_button": [ ]
},
{
"type": "view",
"name": "个人中心",
"url": "http://p2p.allfree.cc/wap/user/index",
"sub_button": [ ]
}
]
}
 */

class ServerController extends Yaf_Controller_Abstract{
    public $wechat_model = null;
    public $_log = false;//日志开关


    public function init(){
        $this->wechat_model = new WechatServerModel();
        //$this->_log = true;
    }

    #入口
    public function indexAction(){
        if(isset($_GET['echostr'])){
            $echoStr = $_GET['echostr'];
            $signature = $_GET['signature'];
            $timestamp = $_GET['timestamp'];
            $nonce = $_GET['nonce'];
            if($this->wechat_model->checkSignature($signature,$timestamp,$nonce)){
                echo $echoStr;
                exit;
            }
        } else {
            $this->responseMsg();
        }
    }




    #授权  (服务号)
    public function accreditAction(){
        $redirect_uri ="http://".$_SERVER['HTTP_HOST']."/Server/callback";
        $this->wechat_model->Code($redirect_uri);
    }

    #授权回调 (服务号)
    public function callbackAction(){

        $code = $_GET['code'];

        $token = $this->wechat_model->authorization_code($code);

        $access_token = $token['access_token'];
        $s_openid = $token['openid'];

        $wechat_info = $this->wechat_model->snsapi_userinfo($access_token,$s_openid);

        $unionid = $wechat_info['unionid'];

        $user_model = new UserModel();
        $user_info = $user_model->getDataByUnionid($unionid);

        $date['nickname'] = $wechat_info['nickname'];
        $date['sex'] = $wechat_info['sex'];
        $date['headimgurl'] = $wechat_info['headimgurl'];
        $date['s_openid'] = $s_openid;
        if($user_info){
            $user_model->updateData($date,$user_info['uid']);
        }else{
            $date['unionid'] = $unionid;
            $user_model->addData($date);
        }

        $time_out = time() + 60 * 60 * 24 * 30;
        setcookie("unionid", $unionid, $time_out,'/');
        header("Location:/wap/user/index") ;exit;
    }






    #处理请求并响应
    protected function responseMsg(){
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];

        if (!empty($postStr)){
            // 获取报文传送方式
            $cryptType = isset($_GET['encrypt_type']) ? $_GET['encrypt_type'] : 'none';
            switch($cryptType){
                case 'none':
                case 'raw': // 明文模式
                    $resultStr = $this->noneEncrypt($postStr);
                    break;
                default:
                    echo '';
                    return ;
            }
            echo $resultStr;
        } else {
            echo '';
        }
    }

    #明文方式处理函数
    protected function noneEncrypt($object){
        if (empty($object)){
            return '';
        }

        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($object, 'SimpleXMLElement', LIBXML_NOCDATA);
        $tx_type = trim($postObj->MsgType);

        switch($tx_type){
            case 'text':
                $resultStr = $this->receiveText($postObj);
                break;
            case 'image':
                $resultStr = $this->receiveImage($postObj);
                break;
            case 'location':
                $resultStr = $this->receiveLocation($postObj);
                break;
            case 'voice':
                $resultStr = $this->receiveVoice($postObj);
                break;
            case 'video':
                $resultStr = $this->receiveVideo($postObj);
                break;
            case 'link':
                $resultStr = $this->receiveLink($postObj);
                break;
            case 'event':
                $resultStr = $this->receiveEvent($postObj);
                break;
            default:
                $resultStr = '';    // 直接回复空串,公众号平台将忽略用户的请求
                break;
        }

        return $resultStr;
    }



    /***************************************************
     * 收发消息处理
     ***************************************************/
    #处理用户文本消息
    protected function receiveText($object) {
        $keyword = addslashes(trim($object->Content));

//        //发红包
//        $retContent = $this->redPack($object,$keyword);
//        if($retContent){
//            $retContent = $this->wechat_model->transmitText($object, $retContent);
//            return $retContent;
//        }

        if($keyword == '粉丝群'){
            $retContent = 'BoOdMxrsvmFLaVlNTz7pOTHbkXcK0BVTJ3LoJJq8TeMEh5-SxWb0SiYYI6HHhSY7';
            $retContent = htmlspecialchars_decode(stripslashes($retContent));
            $retContent = $this->wechat_model->transmitPic($object, $retContent);
            return $retContent;
        } else {
            $retContent = '亲，有任何问题请加我们的理财群咨询客服哦，回复“粉丝群”即可入群[飞吻]';
        }
        $retContent = $this->wechat_model->transmitText($object, $retContent);
        return $retContent;
    }

    #处理用户菜单消息
    protected function receiveEvent($object){
        $resultStr = '';
        switch ($object->Event){
            case 'subscribe':   // 用户关注
                $resultStr = $this->focusOn($object);
                break;
            case 'CLICK':       // 菜单点击拉取消息
                switch ($object->EventKey){
                    case 'AD_COOPERATION':        //广告合作
                        $resultStr = $this->ad_cooperation($object);
                        break;
                }
                break;
            case 'VIEW':        // 菜单点击跳转链接
                switch ($object->EventKey){
                    case 'http://p2p.allfree.cc/wap/activity/index':    //活动
                        $resultStr = $this->activity($object);
                        break;
                    case 'http://p2p.allfree.cc/wap/user/index':        //用户
                        $resultStr = $this->user($object);
                        break;
                }
                break;
            default:
                break;
        }
        return $resultStr;
    }

    protected function receiveImage($object){
        return '';
    }

    protected function receiveLocation($object){
        return '';
    }

    protected function receiveVoice($object){
        return '';
    }

    protected function receiveVideo($object){
        return '';
    }

    protected function receiveLink($object){
        return '';
    }






    /***************************************************
     * 事件业务逻辑
     ***************************************************/
    #用户关注
    protected function focusOn($object){
        $token_arr = $this->wechat_model->getAccessToken();
        $access_token = $token_arr['access_token'];
        $wechat_info = $this->wechat_model->getUserInfo($access_token,$object->FromUserName);

        $unionid = $wechat_info['unionid'];
        $user_model = new UserModel();
        $user_info = $user_model->getDataByUnionid($unionid);

        $date['nickname'] = $wechat_info['nickname'];
        $date['sex'] = $wechat_info['sex'];
        $date['headimgurl'] = $wechat_info['headimgurl'];
        $date['s_openid'] = $wechat_info['openid'];
        if($user_info){
            $user_model->updateData($date,$user_info['uid']);
        }else{
            $date['unionid'] = $unionid;
            $user_model->addData($date);
        }

        $log_str = date('Ymd H:i:s',time())."关注,openid:".$object->FromUserName."\n";
        $this->log($log_str);

        $content = '终于等到你，以后也不要离开我哦！[害羞]理财节火热进行中，<a href="http://p2p.allfree.cc/wap/activity/index">点击参与~</a>开启赚钱模式喽[强]';
        return $this->wechat_model->transmitText($object, $content);
    }

    #广告合作
    protected function ad_cooperation($object){
        $context = '请联系QQ：2850771424' . "\n"
            . '（备注请说明广告合作）'. "\n"
            . '欢迎各类商家广告和推广合作！';
        return $this->wechat_model->transmitText($object, $context);
    }

    #活动
    protected function activity($object){
        $context = date('Ymd H:i:s',time())."访问活动,openid:".$object->FromUserName."\n";

        $this->log($context);
        return '';
    }

    #用户
    protected function user($object){
        $log_str = date('Ymd H:i:s',time())."访问个人中心,openid:".$object->FromUserName."\n";

        $this->log($log_str);
        return '';
    }


    #发红包
    protected function redPack($object,$keyword){

        $keyword_array = array(
            '测试红包'=>array(
                'batch'=>0,
                'money'=>0,//单位(分)
                'time'=>20170206,
            ),

        );
        $red_pack_config = $keyword_array[$keyword];
        if($red_pack_config){

            $curtime = time();
            if(date('Ymd',$curtime)<$red_pack_config['time']){//时间未到
                $retContent = '时间未到';
                return $retContent;
            }

            $red_pack_model = new WechatRedPackModel();
            $apply = $red_pack_model->checkApply($object->FromUserName,$red_pack_config['batch']);
            #判断是否已经参与
            if($apply){//参加过了
                $retContent = '你已经参加过了哦';
                return $retContent;
            }


            $money = $red_pack_config['money'];
            if($money==0){//发完了
                $retContent = '已经发完啦,下次早点来';
                return $retContent;
            }

            $insert_red_pack['openid'] = $object->FromUserName;
            $insert_red_pack['batch'] = $red_pack_config['batch'];
            $insert_red_pack['money'] = $money;
            $insert_red_pack['status'] = WechatRedPackModel::$STATUS['INIT'];
            $insert_red_pack['remark'] = '';
            $insert_red_pack['created_at'] = $curtime;
            $red_pack_id = $red_pack_model->addData($insert_red_pack);


            #构造请求参数
            $nonce_str = strtoupper( md5($curtime . mt_rand(0,1000)) );
            $req_param=array();
            $req_param['nonce_str'] = $nonce_str;
            $req_param['mch_billno'] = date('Ymd',$curtime).sprintf('%010d', $red_pack_id);
            $req_param['re_openid'] = $object->FromUserName;
            $req_param['total_amount'] = $money;
            $req_param['total_num'] = '1';
            $req_param['wishing'] = '祝福语';
            $req_param['act_name'] = '活动名称';
            $req_param['remark'] = '备注';

            $resp_ary = $this->wechat_model->sendRedPack($req_param);

            #解析响应
            $update_data = array();
            if(isset($resp_ary['return_code']) && isset($resp_ary['result_code'])){
                if($resp_ary['return_code'] == 'SUCCESS' && $resp_ary['result_code'] == 'SUCCESS'){
                    $red_pack_id = isset($resp_ary['mch_billno']) ? intval(substr($resp_ary['mch_billno'], 0, 10)) : $red_pack_id;
                    $wechat_trans_no = isset($resp_ary['send_listid']) ? $resp_ary['send_listid'] : '';
                    $update_data['status'] = WechatRedPackModel::$STATUS['REQSUCC'];
                    $update_data['remark'] = '微信成功支付,支付订单号:' . $wechat_trans_no;
                } elseif($resp_ary['return_code'] != 'SUCCESS') {
                    $err_msg = isset($resp_ary['return_msg']) ? $resp_ary['return_msg'] : '微信未返回return_code错误信息';
                    $update_data['status'] = WechatRedPackModel::$STATUS['REQFAIL'];
                    $update_data['remark'] = $err_msg;
                } elseif($resp_ary['result_code'] != 'SUCCESS'){
                    $err_msg = isset($resp_ary['err_code_des']) ? $resp_ary['err_code_des'] : '微信未返回err_code错误信息';
                    $update_data['status'] = WechatRedPackModel::$STATUS['REQFAIL'];
                    $update_data['remark'] = $err_msg;
                }
            } else {
                $update_data['status'] = WechatRedPackModel::$STATUS['REQFAIL'];
                $update_data['remark'] = '微信返回非协议格式数据';
            }

            $red_pack_model->updateData($update_data,$red_pack_id);

            if($update_data['status'] == WechatRedPackModel::$STATUS['REQSUCC']) {
                $retContent = '全民免费祝你新年快乐';
                return $retContent;
            } else {
                $retContent = '发送失败,联系客服,编号:'.$red_pack_id;
                return $retContent;
            }
        }
    }





    private function log($content='') {
        if($this->_log) {
            $fp = fopen('/tmp/wechat_p2p_service.log', 'a');
            if (!$fp) {
                return;
            }
            fwrite($fp, $content);
            fclose($fp);
        }
    }
}
?>
