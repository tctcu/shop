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

    public function __construct($type = 1){
        $taobao_config = Yaf_Registry::get("config")->get('taobao.sdk.'.$type);
        $this->apiClient = new TopClient;

        $this->apiClient->appkey = $taobao_config->appkey;
        $this->apiClient->secretKey = $taobao_config->secretKey;
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
        $resp = json_decode(json_encode($resp),true);

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
    function TbkItemInfoGetRequest($num_iids = array()){
        if(empty($num_iids) && !is_array($num_iids)){
            return [];
        }
        $req = new TbkItemInfoGetRequest;
        //$req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url");
        $req->setPlatform("2");
        $req->setNumIids("".implode(',',$num_iids)."");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);

        if(isset($resp['results']['n_tbk_item'][0]) && !empty($resp['results']['n_tbk_item'][0])){
            $retData = $resp['results']['n_tbk_item'][0];
        } else {
            $retData = [];
        }

        return $retData;
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

    #关键词搜索
    function TbkItemGetRequest($condition = array()){
        $keyword = isset($condition['keyword']) ? trim($condition['keyword']) : '';//关键词
        if(empty($keyword)){
            return array();
        }

        $req = new TbkItemGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
        $req->setQ("$keyword");
//        $req->setCat("16,18");
//        $req->setItemloc("杭州");
//        $req->setSort("tk_rate_des");
//        $req->setIsTmall("false");
//        $req->setIsOverseas("false");
//        $req->setStartPrice("10");
//        $req->setEndPrice("10");
//        $req->setStartTkRate("123");
//        $req->setEndTkRate("123");
//        $req->setPlatform("1");
//        $req->setPageNo("123");
        $req->setPageSize("100");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        if(isset($resp['results']['n_tbk_item'][0]) && !empty($resp['results']['n_tbk_item'][0])){
            $retData = $resp['results']['n_tbk_item'][0];
            foreach($resp['results']['n_tbk_item'] as $val){
                if($val['title'] == $keyword){
                    $retData = $val;
                    break;
                }
            }
        } else {
            $retData = array();
        }

        return $retData;
    }

    #创建淘口令
    function TbkTpwdCreateRequest($condition = array()){
        $text = isset($condition['text']) ? trim($condition['text']) : '';//关键词
        $url = isset($condition['url']) ? trim($condition['url']) : '';//链接
        $logo = isset($condition['logo']) ? trim($condition['logo']) : '';//图片
        if(empty($text) || empty($url) || empty($logo)){
            return false;
        }
        $req = new TbkTpwdCreateRequest;
        //$req->setUserId("123");
        //$req->setExt("{}");
        $req->setText("$text");
        $req->setUrl("$url");
        $req->setLogo("$logo");

        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        if(isset($resp['data']['model']) && !empty($resp['data']['model'])){
            $retData = $resp['data']['model'];
        } else {
            $retData = array();
        }

        return $retData;
    }


    #淘口令转链
    function TbkTpwdConvertRequest($tpwd){
        //$adzone_id = '65740777';//小麦我的ta
        $adzone_id = '59195850301';//券购

        $req = new TbkTpwdConvertRequest;
        $req->setPasswordContent("$tpwd");
        $req->setAdzoneId("$adzone_id");
        //$req->setDx("1");//优先定向

        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        $retData = [];
        if (isset($resp['data']['num_iid'])) {
            $retData = $resp['data']['num_iid'];
        }

        return $retData;
    }



    #链接转换
    function TbkItemConvertRequest($numIids){
        $adzone_id = '59195850301';//券购
        $req = new TbkItemConvertRequest;
        $req->setFields("num_iid,click_url");
        $req->setNumIids("$numIids");
        $req->setAdzoneId("$adzone_id");
        //$req->setPlatform("123");
        //$req->setUnid("demo");
        //$req->setDx("1");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        echo '<pre>';
        print_r($resp);die;
    }



    #获取订单
    function TbkOrderGetRequest($start, $page = 1, $pageSize = 100){
        $req = new TbkOrderGetRequest();
        $req->setFields("trade_parent_id,trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk_status,tk3rd_type,tk3rd_pub_id,order_type,income_rate,pub_share_pre_fee,subsidy_rate,subsidy_type,terminal_type,auction_category,site_idString,site_name,adzone_id,adzone_name,alipay_total_price,total_commission_rate,total_commission_fee,subsidy_fee,relation_id,special_id,click_time");
        $req->setStartTime("$start");
        $req->setSpan("1200");
        $req->setPageNo("$page");
        $req->setPageSize("$pageSize");
        $req->setTkStatus("1");
        $req->setOrderQueryType("create_time");
        $req->setOrderScene("3");//1:常规订单，2:渠道订单，3:会员运营订单
        //$req->setOrderCountType("1");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        $retData = [];
        if (isset($resp['results']['n_tbk_order'])) {
            $retData = $resp['results']['n_tbk_order'];
        }

        return $retData;
    }

    #新获取订单
    function TbkOrderDetailsGetRequest($start, $end, $page = 1, $pageSize = 100){
        $req = new TbkOrderDetailsGetRequest;
        $req->setQueryType("1");//1：按照订单淘客创建时间查询，2:按照订单淘客付款时间查询，3:按照订单淘客结算时间查询
        //$req->setPositionIndex("2222_334666");//位点，除第一页之外，都需要传递；前端原样返回。
        $req->setPageSize("$pageSize");
        //$req->setMemberType("2");//2:二方，3:三方，不传，表示所有角色
        //$req->setTkStatus("12");//12-付款，13-关闭，14-确认收货，15-结算成功;不传，表示所有状态
        $req->setStartTime("$start");
        $req->setEndTime("$end");
        $req->setJumpType("1");//跳转类型，当向前或者向后翻页必须提供,-1: 向前翻页,1：向后翻页
        $req->setPageNo("$page");
        $req->setOrderScene("1");//1:常规订单，2:渠道订单，3:会员运营订单，默认为1
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        echo '<pre>';
        print_r($resp);die;
        $retData = [];
        if (isset($resp['results']['n_tbk_order'])) {
            $retData = $resp['results']['n_tbk_order'];
        }

        return $retData;
    }


    #获取渠道邀请码 川律渠道-SR3HPL 川律会员-DK8CHM
    function TbkScInvitecodeGetRequest($session){
        $req = new TbkScInvitecodeGetRequest;
        $req->setRelationId("11");
        $req->setRelationApp("common");
        $req->setCodeType("3");//1-渠道 2-裂变 3-会员
        $resp = $this->apiClient->execute($req, $session);
        $resp = json_decode(json_encode($resp),true);
        echo '<pre>';
        print_r($resp);die;
        $retData = [];
        if (isset($resp['data'])) {
            $retData = $resp['data'];
        }

        return $retData;
    }


    #绑定渠道关系
    function TbkScPublisherInfoSaveRequest($session,$scene=3,$note='备注'){
        $scene_map = [
            '2' =>'SR3HPL',//川律渠道
            '3' =>'DK8CHM',//川律会员
        ];
        $req = new TbkScPublisherInfoSaveRequest;
        //$req->setRelationFrom("123");
        //$req->setOfflineScene("4");
        //$req->setOnlineScene("$scene");
        $req->setInviterCode($scene_map[$scene]);// 川律渠道-SR3HPL 川律会员-DK8CHM
        $req->setInfoType("1");
        $req->setNote($note);
        $resp = $this->apiClient->execute($req, $session);
        $resp = json_decode(json_encode($resp),true);
        $retData = [];
        if (isset($resp['data'])) {
            $retData = $resp['data'];
        }

        return $retData;
    }


    function TbkDgMaterialOptionalRequest(){
        $req = new TbkDgMaterialOptionalRequest;
        $req->setQ("手机壳");
        $req->setAdzoneId("108937250396");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        return $resp;
    }

    #获取渠道关系列表
    function TbkScPublisherInfoGetRequest($page = 1, $pageSize = 10){
        $session = '6101f289408a6ad0cd510ec7423b04005246198251c62a34227738592';//川律
        $session = '61023227e03793a9e8e872269f996a1351a5ab4abe44442418362049';//小麦
        $req = new TbkScPublisherInfoGetRequest;
        $req->setInfoType("2");//1-渠道 2-会员
        $req->setPageNo("$page");
        $req->setPageSize("$pageSize");
        $req->setRelationApp("common");
        $resp = $this->apiClient->execute($req, $session);
        $resp = json_decode(json_encode($resp),true);
        $retData = [];
        if (isset($resp['data']['inviter_list']['map_data'])) {
            $retData = $resp['data']['inviter_list']['map_data'];
        }
        return $retData;
    }

    #淘礼金
    function TbkDgVegasTljCreateRequest($itemId){
        $req = new TbkDgVegasTljCreateRequest;
        $req->setCampaignType("MKT");//定向：DX；鹊桥：LINK_EVENT；营销：MKT
        $req->setAdzoneId("73240000099");//banner4
        $req->setItemId("$itemId");//589123348070 568127651115
        $req->setTotalNum("20");
        $req->setName("券购APP专属福利");
        $req->setUserTotalWinNumLimit("1");
        $req->setSecuritySwitch("true");//启用安全
        $req->setPerFace("1.3");//现阶段单个红包面额必须大于或等于1元，小于49999元，可支持2位小数点
        $req->setSendStartTime("2019-06-14 00:00:00");
        $req->setSendEndTime("2019-06-16 00:00:00");
        $req->setUseEndTime("1");
        $req->setUseEndTimeMode("1");
        $req->setUseStartTime("2019-06-14");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        echo '<pre>';
        print_r($resp);die;
        $retData = [];
        if (isset($resp['result']['model'])) {
            $retData = $resp['result']['model'];
        }
        return $retData;
    }

    #淘礼金查询
    function TbkDgVegasTljInstanceReportRequest($rightsId){
        $req = new TbkDgVegasTljInstanceReportRequest;
        $req->setRightsId("$rightsId");
        $resp = $this->apiClient->execute($req);
        echo '<pre>';
        print_r($resp);die;
        $resp = json_decode(json_encode($resp),true);
        $retData = [];
        if (isset($resp['result'])) {
            $retData = $resp['result'];
        }
        return $retData;
    }


    #账号间uid切换
    function OpenuidChangeRequest($openUid){
        $req = new OpenuidChangeRequest;
        $req->setOpenUid($openUid);
        $req->setTargetAppKey("25363435");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        echo '<pre>';
        print_r($resp);die;
    }

    #当前登录UID
    function OpenuidGetRequest($sessionKey){
        $req = new OpenuidGetRequest;
        $resp = $this->apiClient->execute($req,$sessionKey);
        $resp = json_decode(json_encode($resp),true);
        echo '<pre>';
        print_r($resp);die;
    }

    #h5授权登录换取token 需要https
    function TopAuthTokenCreateRequest($code){
        $req = new TopAuthTokenCreateRequest;
        $req->setCode("$code");
        //$req->setUuid("abc");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        echo '<pre>';
        print_r($resp);die;
    }

    #刷新token
    function TopAuthTokenRefreshRequest($refreshToken){
        $req = new TopAuthTokenRefreshRequest;
        $req->setRefreshToken("$refreshToken");
        $resp = $this->apiClient->execute($req);
        $resp = json_decode(json_encode($resp),true);
        echo '<pre>';
        print_r($resp);die;
    }

    #code 换取 token
    function code2token($code){
        $url = 'https://oauth.taobao.com/token';
        $postfields = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->apiClient->appkey ,
            'client_secret' => $this->apiClient->secretKey,
            'code' => $code,
            'redirect_uri' => 'http://shop.clhuo.com/test/tbredirecttoken'
        ];
        $post_data = '';

        foreach($postfields as $key=>$value){
            $post_data .="$key=".urlencode($value)."&";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);

        //添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post_data,0,-1));
        $output = curl_exec($ch);
        //状态码
        //$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $tb_info = json_decode($output,true);
        foreach($tb_info as $key=>$value){
            $tb_info[$key] = urldecode($value);
        }
        return $tb_info;
    }





    #格式化淘宝数据
    function makeTb($item_info,$url_info)
    {
        $data = [
            'itemid' => $item_info['num_iid'].'',
            'itemshorttitle' => $item_info['title'],
            'itemdesc' => $item_info['title'],
            'itemprice' => $item_info['zk_final_price'].'',
            'itemsale' => $item_info['volume'].'',
            'itempic' => $item_info['pict_url'] . '_600x600q90.jpg',
            'itemendprice' => $item_info['zk_final_price'],
            'url' => $url_info['item_url'],
            'coupon_type' => '0',//券状态
            'couponmoney' => '',
            'couponexplain' => '',
            'couponstarttime' => '',
            'couponendtime' => '',
            'shoptype' => $item_info['user_type'] == 1 ? 'B': 'C',
            'taobao_image' => $item_info['small_images']['string']
        ];
        if($url_info['coupon_type']){ //有券
            $couponmoney = 0;
            #获取券价格
            if(preg_match ('#减([\d]+)元#is', $url_info['coupon_info'], $m) !== false ){//券价
                $couponmoney = $m[1];
            }
            $data['coupon_type'] = $url_info['coupon_type'].'';
            $data['itemendprice'] = ($data['itemendprice']-$couponmoney).'';
            $data['url'] = $url_info['coupon_click_url'];
            $data['couponmoney'] = $couponmoney.'';
            $data['couponexplain'] = $url_info['coupon_info'];
            $data['couponstarttime'] = strtotime($url_info['coupon_start_time']).'';
            $data['couponendtime'] = strtotime($url_info['coupon_end_time']).'';
        }
        $data['rebate'] = sprintf("%.2f",$url_info['max_commission_rate'] * ConfigModel::RATE * $data['itemendprice'] * ConfigModel::REBATE);
        return $data;
    }


}