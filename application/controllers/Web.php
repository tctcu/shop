<?php
class WebController extends Yaf_Controller_Abstract
{
    private $pid = 'mm_234440039_166200410_57891600477';//'mm_116356778_18618211_65740777';

    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    #协议
    function protocolAction(){}
    #引导识别淘口令
    function courseAction(){}
    #返利规则
    function rebateRuleAction(){}
    #淘宝分享详情
    function shareDetailAction(){
        $itemid =  intval($_REQUEST['itemid']);
        $tkl =  trim($_REQUEST['tkl']);
        //$tkl =  '￥miWFbqdOWkc￥';
        if(empty($itemid)){
            echo 404;die;
        }
        $url = "http://v2.api.haodanku.com/item_detail/apikey/allfree/itemid/" . $itemid;
        $json = file_get_contents($url);
        $tb_info = json_decode($json, true)['data'];
        $taobao_model = new TaobaoModel();

        if(empty($tb_info)){//查库
            $tb_model = new TbModel();
            $tb_info = $tb_model->getDataByItemId($itemid);
        }
        if(empty($tb_info['taobao_image'])){//淘宝图片
            $condition = [
                'item_id' => $itemid
            ];
            $item_info = $taobao_model->TbkItemInfoGetRequest($condition);
            $tb_info['taobao_image'] = implode(',', $item_info['small_images']['string']);

            if($tb_model) {
                $tb_update = [
                    'taobao_image' => $tb_info['taobao_image']
                ];
                $tb_model->updateData($tb_update, $tb_info['id']);
            }
        }

        if(empty($tb_info)){//查淘宝
            $condition = [
                $itemid
            ];
            $item_info = $taobao_model->TbkItemInfoGetRequest($condition);
            $condition = [
                'item_id' => $itemid
            ];
            $yuque_model = new YuQueModel();
            $url_info = $yuque_model->privilegeGet($condition);
            $tb_info = $taobao_model->makeTb($item_info,$url_info);
            $tb_info['taobao_image'] = implode(',',$item_info['small_images']['string']);
            $tb_info['tkrates'] = $url_info['max_commission_rate'];
            $tb_info['shopname'] = $item_info['nick'];
        } else {//好单库的商品都有优惠券
            $tb_info['coupon_type'] = '1';//优惠券状态 0-没有券
        }
        $tb_detail_model = new TbDetailModel();
        $tb_detail_info = $tb_detail_model->getDataByItemId($itemid);
        $tb_info['taobao_detail'] = $tb_detail_info['taobao_detail'];
        $data = $tb_detail_model->makeDetail($tb_info);

        $data['url'] = 'http://uland.taobao.com/coupon/edetail?activityId=' . $tb_info['activityid'] . '&itemId=' . $tb_info['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid;

        if($tkl){
            $tbk_info = json_decode(file_get_contents(ConfigModel::TKL_URL.$tkl),true);
            $data['url'] = $tbk_info['url'];
        }

        $this->_view->tb_info = $data;
    }
    #邀请好友
    function inviteAction(){
        $invite_code = trim($_REQUEST['invite_code']);
        $user_model = new UserModel();
        $uid = $user_model->code2uid($invite_code);
        if(empty($uid)){
            die(404);
        }
        $user_info = $user_model->getDataByUid($uid);
        if(empty($user_info)){
            die(404);
        }
        $user_info['invite_code'] = $invite_code;

        $user_info['accredit_url'] = "http://".$_SERVER['HTTP_HOST']."/web/register?invite_code=".$invite_code;
        $this->_view->user_info = $user_info;
    }

    #app外微信注册
    function registerAction(){
        $invite_code =  trim($_REQUEST['invite_code']);
        $wechat_model = new WechatServerModel();
        $redirect_uri ="http://".$_SERVER['HTTP_HOST']."/web/callback";
        $wechat_model->Code($redirect_uri,$invite_code);
    }

    #微信注册授权回调
    function callbackAction(){
        $wechat_model = new WechatServerModel();
        $token = $wechat_model->authorization_code($_GET['code']);
        $invite_code = $_GET['state'];
        $user_model = new UserModel();
        $up_uid = $user_model->code2uid($invite_code);
        $user_data = $wechat_model->snsapi_userinfo($token['access_token'],$token['openid']);

        $user_info = $user_model->getDataByUnionid($user_data['unionid']);
        if(empty($user_info)){//注册
            $insert = [
                "up_uid" => intval($up_uid),
                "w_openid" => $user_data["openid"],
                "w_nickname" => $user_data["nickname"],
                "w_sex" => $user_data["sex"],
                "w_city" => $user_data["city"],
                "w_province" => $user_data["province"],
                "w_country" => $user_data["country"],
                "w_headimgurl" => $user_data["headimgurl"],
                "w_unionid" => $user_data["unionid"]
            ];
            $user_model->addData($insert);
            echo '注册';
        } else {
            //更新信息
            $update = [
                "w_nickname" => $user_data["nickname"],
                "w_sex" => $user_data["sex"],
                "w_city" => $user_data["city"],
                "w_province" => $user_data["province"],
                "w_country" => $user_data["country"],
                "w_headimgurl" => $user_data["headimgurl"],
            ];
            $user_model->updateData($update,$user_info['uid']);
            echo '登录';
        }
        sleep(1);
        header("Location:https://a.app.qq.com/o/simple.jsp?pkgname=com.jinchuan.ec&fromcase=40003");exit;
    }


    #拼多多回调
    function pddAction(){
        echo  'success';die;
    }


}
