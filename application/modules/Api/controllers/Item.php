<?php

#商品
class ItemController extends ApiController
{
    function init()
    {
        parent::init();
    }

    #列表
    function listAction()
    {
        $cid = intval($_REQUEST['cid']) ? intval($_REQUEST['cid']) : 0;
        $min_id = intval($_REQUEST['min_id']) ? intval($_REQUEST['min_id']) : 1;
        $pageSize = intval($_REQUEST['pageSize']) ? intval($_REQUEST['pageSize']) : 20;

        $condition = [
            'status' => 1,
            'fqcat' => $cid,
            'min_id' => $min_id,
        ];

        $tb_model = new TbModel();
        $tb_list = $tb_model->getListData($pageSize,$condition);
        $data = $tb_model->makeList($tb_list);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #详情
    function detailAction()
    {
        $itemid = intval($_REQUEST['itemid']);
        $url = "http://v2.api.haodanku.com/item_detail/apikey/allfree/itemid/" . $itemid;
        $json = file_get_contents($url);
        $tb_info = json_decode($json, true)['data'];

        if(empty($tb_info)){//查库
            $tb_model = new TbModel();
            $tb_info = $tb_model->getDataByItemId($itemid);
        }

        if(empty($tb_info)){//查淘宝
            $taobao_model = new TaobaoModel();
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

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #跟单获取连接
    function getUserUrlAction(){
        $itemid = intval($_REQUEST['itemid']);

        $uid = $this->uid;
$uid = 2;
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
        $type = TbModel::MEMBER[$pid_info['memberid_id']];

        $condition = [
            'item_id' => $itemid,
            'session' => Yaf_Registry::get("config")->get('taobao.account.'.$type)->session,
            'site_id' => $pid_info['site_id'],
            'adzone_id' => $pid_info['adzone_id']
        ];
        $yuque_model = new YuQueModel($type);
        $url_info = $yuque_model->privilegeGet($condition);
        $data = [];
        if($url_info['coupon_click_url']) {
            $data = [
                'item_id' => $itemid,
                'url' => $url_info['coupon_click_url']
            ];
        }

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }


    #客户端维护淘宝详情页
    function updateDetailAction(){
        $itemid = intval($_REQUEST['itemid']);
        $taobao_detail = implode(',',explode(',',trim($_REQUEST['taobao_detail'])));
        if($itemid && $taobao_detail){
            $tb_detail_model = new TbDetailModel();
            $add = [
                'itemid' => $itemid,
                'taobao_detail' => $taobao_detail,
            ];
            $tb_detail_model->addData($add);
        }
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
    }

}