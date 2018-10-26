<?php
include_once(dirname(dirname(__FILE__)) . '/library/taobao-sdk/TopSdk.php'); // 加载淘宝sdk
date_default_timezone_set('Asia/Shanghai');
/**
 * 淘宝模型
 */

class TaobaoModel{
    private $apiClient;

    private $order_status_message = array(
        1=>'',//'WAIT_BUYER_PAY',//等待买家付款
        2=>'taobao_tae_BaichuanTradePaidDone',//'WAIT_SELLER_SEND_GOODS',//等待卖家发货  付款成功(下单已付款)
        3=>'',//'SELLER_CONSIGNED_PART',//卖家部分发货
        4=>'taobao_tae_BaichuanTradeClosed',//'WAIT_BUYER_CONFIRM_GOODS',//等待买家确认收货 退款后交易关闭
        5=>'',//'TRADE_BUYER_SIGNED',//买家已签收（货到付款专用）
        6=>'taobao_tae_BaichuanTradeSuccess',//'TRADE_FINISHED',//交易成功    交易成功消息(确认收货后)
        7=>'taobao_tae_BaichuanTradeCreated',//'TRADE_CLOSED'交易关闭   创建订单消息(下单未付款)
        8=>'taobao_tae_BaichuanTradeClosed',//'TRADE_CLOSED_BY_TAOBAO',//交易被淘宝关闭  创建订单后交易关闭
        9=>'',//'TRADE_NO_CREATE_PAY',//没有创建外部交易（支付宝交易）
        10=>'',//'WAIT_PRE_AUTH_CONFIRM',//余额宝0元购合约中
        11=>'',//'PAY_PENDING',//外卡支付付款确认中
        12=>'',//'ALL_WAIT_PAY',//所有买家未付款的交易（包含,//WAIT_BUYER_PAY、TRADE_NO_CREATE_PAY）
        13=>'',//'ALL_CLOSED',//所有关闭的交易（包含,//TRADE_CLOSED、TRADE_CLOSED_BY_TAOBAO）

        50=>'taobao_tae_BaichuanTradeRefundCreated',//买家点击退款按钮后促发
        51=>'taobao_tae_BaichuanTradeRefundSuccess',//退款成功
    );

    public function __construct(){
        $this->apiClient = new TopClient;
//        $this->apiClient->appkey = '23390742';
//        $this->apiClient->secretKey = '0843c0bebd1bbc0aaa3b3812eeb1035b';
//        $this->apiClient->appkey = '24844090';
//        $this->apiClient->secretKey = 'bc0248ba377330b7d4afab9d3d19c421';
        $this->apiClient->appkey = '23399350';
        $this->apiClient->secretKey = '58e224733d0fcbd0e98a86437cc84eed';
        $this->apiClient->format = 'json';

    }

    #商品列表服务
    function TaeItemsListRequest($condition =array()){
        $num_iid = isset($condition['num_iid']) ? intval($condition['num_iid']) : '';//淘宝商品id
        $t_iid = isset($condition['t_iid']) ? trim($condition['t_iid']) : '';//淘宝商品混淆id
        if(empty($num_iid) && empty($t_iid)){
            return array();
        }

        $req = new TaeItemsListRequest;
        $req->setFields("nick,pic_url,cid,price,promoted_service,promoted_service");
        if($num_iid){
            $req->setNumIids("$num_iid");
        }else{
            $req->setOpenIids("$t_iid");
        }

        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        if(isset($resp['items']['x_item'][0]) && !empty($resp['items']['x_item'][0])){
            $retData = $resp['items']['x_item'][0];
        } else {
            $retData = array();
        }

        return $retData;
    }


    #获取消息
    function TmcMessagesConsumeRequest(){
        $req = new TmcMessagesConsumeRequest;
        //$req->setGroupName("");
        $req->setQuantity("200");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);

        if(isset($resp['messages']['tmc_message']) && !empty($resp['messages']['tmc_message'])){
            $retData = $resp['messages']['tmc_message'];
        } else {
            $retData = array();
        }

        return $retData;
    }

    #确认消息
    function TmcMessagesConfirmRequest($mes_arr = array()){
        $mes_str = implode(',',$mes_arr);

        $req = new TmcMessagesConfirmRequest;
        //$req->setGroupName("");
        $req->setSMessageIds("$mes_str");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);

        if(isset($resp['is_success'])){
            $retData = $resp['is_success'];
        } else {
            $retData = array();
        }

        return $retData;
    }

    #获取消息分组
    function TmcGroupsGetRequest(){
        $req = new TmcGroupsGetRequest;
        //$req->setGroupNames("vip_user");
        $req->setPageNo("1");
        $req->setPageSize("40");
        $resp = $this->apiClient->execute($req);
        echo '<pre>';
        print_r($resp);die;
    }

    #添加分组
    function TmcGroupAddRequest(){
        $req = new TmcGroupAddRequest;
        $req->setGroupName("myself");
        $req->setNicks("小麦我的ta");
        $req->setUserPlatform("tbUIC");
        $resp = $this->apiClient->execute($req);
        echo '<pre>';
        print_r($resp);die;
    }


    #混淆ID获取单个商品详情
    function TaeItemDetailGetRequest($condition =array()){
        $t_iid = isset($condition['t_iid']) ? trim($condition['t_iid']) : '';//淘宝商品混淆id
        if(empty($t_iid)){
            return array();
        }

        $req = new TaeItemDetailGetRequest;
        $req->setBuyerIp("121.40.79.19");
        $req->setFields("itemInfo,priceInfo,skuInfo,stockInfo,rateInfo,descInfo,sellerInfo,mobileDescInfo,deliveryInfo,storeInfo,itemBuyInfo,couponInfo");
        $req->setOpenIid("$t_iid");
        $req->setId("$t_iid");
        $resp = $this->apiClient->execute($req);

        if(isset($resp['data']) && !empty($resp['data'])){
            $retData = $resp['data'];
        } else {
            $retData = array();
        }
        return $retData;
    }


    #生成客服账号
    public function OpenimUsersAddRequest($im_user_id,$password){
        $req = new OpenimUsersAddRequest;
        $userinfos = new Userinfos;

        $userinfos->userid = $im_user_id;
        $userinfos->password = $password;

        $req->setUserinfos(json_encode($userinfos));
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);

        if(isset($resp['uid_succ']['string'][0]) && !empty($resp['uid_succ']['string'][0])){
            $retData = $resp['uid_succ']['string'][0];
        } else {
            $retData = array();
        }
        return $retData;
    }



    #获取cid分类
    public function ItemcatsGetRequest($cid){
        $req = new ItemcatsGetRequest;
        //$req->setCids("18957,19562");
        //$req->setDatetime("2000-01-01 00:00:00");
        $req->setFields("cid,parent_cid,name,is_parent");
        $req->setParentCid("$cid");
        $resp = $this->apiClient->execute($req);

        return $resp['item_cats']['item_cat'];
    }


    #商品详情(简版)
    function TbkItemInfoGetRequest(){
        $req = new TbkItemInfoGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url");
        $req->setPlatform("1");
        $req->setNumIids("565173589700");
        $resp = $this->apiClient->execute($req);
        echo '<pre>';
        print_r($resp);die;
    }

    #获取淘宝券
    function TbkCouponGetRequest($e=''){
        $req = new TbkCouponGetRequest;
        //$req->setMe("$e");
        $req->setItemId("566948404721");
        $req->setActivityId("e7bd44bc163f4641913251f3faa94408");
        $resp = $this->apiClient->execute($req);
        return $resp;
    }


}