<?php
class TestController extends Yaf_Controller_Abstract
{
    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    function addPicAction(){
        $remote_pic  = 'quangouv1.1.2app.apk';
        if($this->getRequest()->isPost()) {
            $model = new CommonModel();
            if (!empty($_FILES['upload_file']) && $_FILES['upload_file']['error'] == 0) {
                $pic = $model->addPic($upload_file = 'upload_file',$remote_pic);
                if (!empty($pic)) {
                   echo CommonModel::IMAGE_URL.$pic;
                } else {
                  echo '上传失败';die;
                }
            }
        }
    }


    function testAction(){

        $model = new UserModel();
        $userId = 923520;
        echo $model->uid2code($userId);
        echo '<hr>';
        echo $model->code2uid('eeee');die;


        echo file_get_contents("https://douban.uieee.com/v2/movie/in_theaters");die;

    }

    function gaoyongAction(){
        $request_url = 'http://v2.api.haodanku.com/ratesurl';
        //$request_url = 'http://v2.api.haodanku.com/super_classify/apikey/allfree';
        $request_data = array();
        $request_data['apikey'] = 'allfree';
        $request_data['itemid'] = '535615570326';
        $request_data['pid'] = 'mm_116356778_18618211_65740777';
        $request_data['activityid'] = '7a08d992d2d545ff9bacc0bb4fc4ed54';

        $res = $this->post_curl($request_url,$request_data);
        echo '<pre>';
        print_r($res);die;
    }


    function post_curl($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result_json = curl_exec($ch);
        curl_close($ch);
        return  json_decode($result_json, true);
    }

    function get_curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result_json = curl_exec($ch);
        curl_close($ch);
        return  json_decode($result_json, true);
    }

    #淘宝详情页
    function tbDetailAction(){
        $item_id = 571503809371;
        $taobao_model = new TaobaoModel(2);
        $condition = [
            'num_iid' => $item_id
        ];
        $item_info = $taobao_model->TaeItemsListRequest($condition);
        echo '<pre>';
        print_r($item_info);die;
        echo '<pre>';
//        print_r($item_info);die;
        $condition = [
            't_iid' => $item_info['open_iid']
        ];
        $item_info = $taobao_model->TaeItemDetailGetRequest($condition);
        print_r($item_info['mobile_desc_info']['desc_list']['desc_fragment']);
        die;

        die;
    }
    //通过md5加密生成签名的函数
    function getSignature($appkey,$appsecret,$date,$tid){
        $string = $appkey.$appsecret.$date.$tid;
        $md5 = md5($string);
        return $md5;
    }
    #详情
    function xqyAction(){
        $val['itemid'] = 569279621940;
       // $val['itemid'] = 535615570326;


        //淘宝详细页
        $detail_info = 'https://acs.m.taobao.com/h5/mtop.taobao.detail.getdetail/6.0/?data=%7B%22itemNumId%22%3A%22'.$val['itemid'].'%22%7D';
        //淘宝图片
        $detail_pic = 'https://h5api.m.taobao.com/h5/mtop.taobao.detail.getdesc/6.0/?data={"id":"'.$val['itemid'].'"}';
        //淘宝图片  加密参数
        $url = 'https://hws.m.taobao.com/d/modulet/v5/WItemMouldDesc.do?id=535615570326&f=TB17qTGyAvoK1RjSZFN8qwxMVXa';

        //瞄有券
        $api = 'http://open.jxb001.cn/tbkopen/detail/tb/api';
        $appkey = '1530477080482482';
        $appsecret = '568176ec67c2d16e69158b2329a5bf29';
        $tbName = '川律网络';
        $api = $api. '?appkey='.$appkey.'&appsecret='.$appsecret.'&tbName='.$tbName.'&itemId='.$val['itemid'];
        //echo $api;die;
        $detail = file_get_contents($api);
        echo '<pre>';
        var_dump($detail);die;

        //api
        $appkey = 'tbweau7kx';
        $appsecret = 'vm9M6IvBizDITNLS';
        $tid = 569279621940;

        $date = date('Y-m-d'); //生成日期
        $signature = $this->getSignature($appkey,$appsecret,$date,$tid); //生成签名
        $data = json_encode(array('tid'=>$tid,'appkey'=>$appkey,'sign'=>$signature)); //输出json字符串到客户端
        $url = 'https://taoapi.ndxiu.com/service/get_detail_full.php?sign=b9d9d8ead9fc10ce03fb9ecc332dfeec&appkey=tbweau7kx&tid=569279621940'.$data;
echo $url;die;
        $detail = $this->get_curl($url);
        echo '<pre>';
        print_r($detail);die;




        $detail = $this->get_curl($detail_api);
        echo '<pre>';
        print_r($detail);die;
        if($detail['data']['item']['images'] && $detail['data']['item']['moduleDescUrl']){

            $taobao_img = 'https:'.implode(',https:',$detail['data']['item']['images']);
            print_r($taobao_img);die;
            $pic_url = 'https:'.$detail['data']['item']['moduleDescUrl'];
            $pic = $this->get_curl($pic_url);
            if($pic['data']['children'] ){
                foreach($pic['data']['children'] as $val){
                    if($val['key'] == 'detail_container_style7'){
                        echo $val['params']['picUrl'].'<br>';
                    }
                }
            }
        }die;
        print_r($detail);die;

        var_dump($aa);die;
    }


    function pidAction(){
        $pid = $_REQUEST['pid'];
        $arr = explode(',',$pid);
        $insert_sql = 'insert into user_pid(memberid_id,site_id,adzone_id,created_at) values';

        foreach($arr as $val){
            $arr = explode('_',$val);

            $insert_sql .= '('.$arr[1].','.$arr[2].','.$arr[3].','.time().'),';
        }

        $insert_sql = rtrim($insert_sql,',');
        echo $insert_sql;die;



        #处理pid
        $json = '';
        $json = str_replace(' ','',$json);
        echo '<pre>';
        $arr = array_column( json_decode($json,true),'adzoneid');
        //print_r($arr);
        foreach($arr as $val){
            echo $val.',';
        }

        die;
    }

    function tklAction(){
        $condition = [
            'text' => '测试淘口令啊',
            'logo' => 'https://img.alicdn.com/bao/uploaded/TB1w.rKLFXXXXamXVXXSutbFXXX.jpg_310x310.jpg',
            'url' => 'https://oauth.taobao.com/authorize?response_type=code&client_id=25363435&redirect_uri=http://dev.tctcv.com/test/tbredirect&state=1212&view=web',
        ];

        $taobao_model = new TaobaoModel(1);
        $res = $taobao_model->TbkTpwdCreateRequest($condition);
        print_r($res);die;
    }


    function tbredirectAction(){
        if(empty($_GET['code'])){
            $this->error('授权发起失败');
        }

        $taobao_model = new TaobaoModel(4);
        $tb_info = $taobao_model->code2token($_GET['code']);
        if(empty($tb_info['access_token'])){
            $this->error('授权回调失败');
        }
        $uid = $_GET['state'];
        $special = $taobao_model->TbkScPublisherInfoSaveRequest($tb_info['access_token'],3,$uid);
        if(empty($special['special_id'])){
            $this->error('会员绑定失败');
        }
        $relation = $taobao_model->TbkScPublisherInfoSaveRequest($tb_info['access_token'],2,$uid);
        $tb_user = [
            'taobao_open_uid' => $tb_info['taobao_open_uid'],
            'taobao_user_nick' => $tb_info['taobao_user_nick'],
            'taobao_user_id' => $tb_info['taobao_user_id'],
            'access_token' => $tb_info['access_token'],
            'refresh_token' => $tb_info['refresh_token'],
            'expire_time' => $tb_info['expire_time'],
            'refresh_token_valid_time' => $tb_info['refresh_token_valid_time'],
            'r2_valid' => $tb_info['r2_valid'],
        ];

        $user_model = new UserModel();
        $tb_user_model = new TbUserModel();
        $user_info = $user_model->getDataByUid($uid);
        if($user_info['special_id']){
            if($special['special_id'] <> $user_info['special_id']){
                $this->error('该账号已经绑定过淘宝XXX');
            } else {//再次授权
                if($relation['relation_id'] <> $user_info['relation_id']){
                    //更新用户渠道关系
                    $user_model->updateData( [
                        'relation_id' => intval($relation['relation_id'])
                    ],$uid);
                }
                //更新授权信息
                $tb_user_info = $tb_user_model->getDataByUid($uid);
                if($tb_user_info){
                    $tb_user_model->updateData($tb_user,$uid);
                } else {//补录记录
                    $tb_user['uid'] = $uid;
                    $tb_user['reamrk'] = '授权补录';
                    $tb_user_model->addData($tb_user);
                }

                $this->success();
            }
        }
        $user_special_info = $user_model->getDataBySpecialId($special['special_id']);
        if($user_special_info['uid'] && $user_special_info['uid']<>$uid){
            $this->error('该淘宝号已被其他用户XXX绑定');
        }
        if(empty($user_info)) {
            $this->error('授权关联失败');
        }
        //更新用户会员渠道关系
        $user_model->updateData( [
            'special_id' => $special['special_id'],
            'relation_id' => intval($relation['relation_id'])
        ],$uid);
        //记录用户淘宝授权信息
        $tb_user['uid'] = $uid;
        $tb_user_model->addData($tb_user);
        $this->success();
    }

    function success(){
        echo "<script type='text/javascript'>
            alert('授权成功');
            setTimeout('close()',10);
            function close(){
                var a = NativeInterface.authTaobaoSuccess();
            }';</script>";
        exit;
    }

    function error($msg){
        echo "<script type='text/javascript'>
            alert('授权失败 ".$msg."');
            setTimeout('close()',10);
            function close(){
                var a = NativeInterface.authTaobaoFail();
            }';</script>";
        exit;
    }



    function tbTestAction(){
//        $yuque_model = new YuQueModel();
        $itemid = 590585384795;
//        $condition = [
//            'item_id' => $itemid
//        ];
//        $url_info = $yuque_model->privilegeGet($condition);
//        print_r($url_info);die;

        $start = '2019-05-23 16:10:00';
        $end = '2019-05-24 16:40:00';
        $session = '6102504e1dc419ca0987260ad16b172f2cc3add998dcc653297963538';//tctcu =>小麦我的ta
        //$session = '6101a0139154c29fc84459d97aec2b336d1df2875c490af2200590755065';//小目标 => 川律
        //$session = '61023115441118ffb26126aba8c70423f53cc39c0474e7b418362049';//小麦我的ta => 川律
        $session = '6101411c0a6bba868871dd0df830249bb18e112a383c22d3297963538';//tctcu => 川律
        //$session = '61028237d4b5c1cffec649da64ea4b5185ceab0c03fbaf44227738592';//川律 => 川律
        //$session = '6102225803c7a08b7f097f1ed9955b10f17dbed86db694d418362049';//小麦我的ta => 小麦我的ta
        //$session = '630260330abc950fcdf86fb217f6fff398953cd63c41499199894851';//小郭
        $session = '6200408e2bce0590ZZb25348a6084424d8d351a9253e273199894851';//小郭
        //$session = '63002035f34cc929e29e07b527c76461669e725c3b5c6f7199894851';//小郭
        //$session = $_REQUEST['session'];
        if(empty($session)){
            echo 'empty session';die;
        }
        $taobao_model = new TaobaoModel(4);
        //$res = $taobao_model->TbkScInvitecodeGetRequest($session);//获取邀请码
        //$res = $taobao_model->code2token('Ma8Q3uPfNsPuadl12nLnC1vT23966469');//code 换取 token
        //$res = $taobao_model->OpenuidGetRequest($session);//
        //$res = $taobao_model->OpenuidChangeRequest('AAFp5TOdAHQC8b-5uEwNYFdX');//
        $res = $taobao_model->TbkScPublisherInfoSaveRequest($session,2);//绑定渠道关系
        //$res = $taobao_model->TbkScPublisherInfoGetRequest(1,20);
        //$res = $taobao_model->TbkOrderGetRequest($start,1,20);//订单
        //$res = $taobao_model->TbkTpwdConvertRequest('￥Gw4sY2VcCwH￥');
        //$res = $taobao_model->TbkOrderGetRequest($start,$page = 1, $pageSize = 100);
        //$res = $taobao_model->TbkOrderDetailsGetRequest($start, $end, $page = 1, $pageSize = 100);
        //$res = $taobao_model->TbkItemConvertRequest($itemid);
        $itemid = 566569778934;//594539807516;//594413180913;//568127651115;//589123348070;
        //$res = $taobao_model->TbkDgVegasTljCreateRequest($itemid);
        $rightsId ='om1NH6rCWmhHhq%2BT8Je4cqJ7%2BkHL3AEW';// 'xToGS9oNSOLq6vWhYPq0yKJ7%2BkHL3AEW';//'JKfITYvryQjsilCPCq9i%2BaJ7%2BkHL3AEW';//'om1NH6rCWmhHhq%2BT8Je4cqJ7%2BkHL3AEW';
       // $res = $taobao_model->TbkDgVegasTljInstanceReportRequest($rightsId);
        echo '<pre>';
        print_r($res);die;
    }

    function tbAction(){

        $taobao_model = new TaobaoModel(5);
        $refreshToken = '';
        $start = '2019-05-31 09:20:00';
        $end = '2019-05-31 10:20:00';
        $res = $taobao_model->TbkOrderGetRequest($start,1,20);
        //$res = $taobao_model->TbkOrderDetailsGetRequest($start,$end,1,20);
        //$res = $taobao_model->TbkDgMaterialOptionalRequest();
        //$res = $taobao_model->TbkScPublisherInfoGetRequest(1,20);
        echo json_encode($res);die;
        echo '<pre>';
        print_r($res);die;
    }


    function orderAction(){

        $start = '2019-03-26 16:40:00';
        $taobao_model = new YuQueModel();
        $res = $taobao_model->orderGet($start);
        print_r($res);die;
    }


    function sendAction(){
        $mail = new SendMail();

       // var_dump($mail);die;
        $res = $mail->send(['xm0563@qq.com'],'测试','内容');
        var_dump($res);die;
    }

    function detailAction(){
        $json = '<p><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2ZQlyaXXXXXb1XXXXXXXXXXXX_!!849842541.gif" size="750x251" /></p> <p style="color:#000000;text-align:center;"><a href="//taoquan.taobao.com/coupon/unify_apply.htm?sellerId=849842541&amp;activityId=f0ee94c702654f9e861b84e6e736dbf3" target="_blank"><img src="//img.alicdn.com/imgextra/i1/849842541/TB2U1AzcStYBeNjSspkXXbU8VXa_!!849842541.jpg" align="absmiddle" size="750x400" /></a></p> <p style="color:#000000;"><span style="line-height:1.5;background-color:#cccccc;"><span style="background-color:#ffffff;"><span style="font-family:microsoft yahei;color:#ff0000;font-size:24.0px;">《1》保修：<span style="color:#000000;">本店产品</span></span></span></span><span style="background-color:#cccccc;font-family:microsoft yahei;font-size:24.0px;"><span style="background-color:#ffffff;">实体店同步销售，<span style="color:#ffff00;"><span style="background-color:#000000;">15天无理由退换货，终身保修</span></span>（<span style="color:#ff0000;">不管是否人为损坏，此款拉杆箱为可拆卸设计，所有配件均可以独立邮寄过去维修，维修方便</span>）</span></span></p> <p style="color:#000000;"><span style="font-family:microsoft yahei;color:#ff0000;font-size:24.0px;">《2》赠品：<span style="color:#20124d;">现在买送透明<span style="color:#ffff00;"><span style="background-color:#000000;">箱透明箱套，箱贴3大版，托运专用绑箱带，行李牌1个</span></span>哦</span></span><span style="line-height:1.5;">&nbsp;</span></p> <p><span style="color:#ff0000;font-family:microsoft yahei;font-size:23.809525px;line-height:36.0px;">《3》</span><strong style="color:#000000;line-height:1.5;"><span style="font-family:microsoft yahei;"><span style="font-size:24.0px;">急速发货：下午5点前拍下当天发出。</span></span></strong></p> <p>&nbsp;</p> <p style="text-align:center;"><span style="font-family:microsoft yahei;"><span style="color:#ff0000;"><span style="font-size:48.0px;"><strong><span style="background-color:#ffff00;">》》新增全新复古款《《</span></strong></span></span></span></p> <p style="text-align:center;"><span style="font-family:microsoft yahei;"><span style="color:#9900ff;"><span style="font-size:24.0px;"><strong><span style="background-color:#ffff00;">特点：优质皮手把、方角、进口全金属密码锁</span></strong></span></span></span></p> <p><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2cfZ1XY.b61Bjy0FnXXaEpXXa_!!849842541.gif" size="750x772"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2c2sZX_ka61Bjy0FgXXbPpVXa_!!849842541.gif" size="750x766"><img align="absmiddle" src="//img.alicdn.com/imgextra/i3/849842541/TB25i.1X4UX61BjSszeXXbpQpXa_!!849842541.gif" size="750x762"><img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB2AfhvdvOM.eBjSZFqXXculVXa_!!849842541.jpg" size="750x753"> <img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2KIOGamiJ.eBjSspfXXbBKFXa_!!849842541.gif" size="750x912"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB22pqQahuI.eBjy0FdXXXgbVXa_!!849842541.gif" size="750x880"><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2gjPkcH1K.eBjSsphXXcJOXXa_!!849842541.jpg" size="750x753"> <img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2rP63sXXXXXXsXXXXXXXXXXXX_!!849842541.jpg" size="750x900"><img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB2IU5Vb4vzQeBjSZFEXXbYEpXa_!!849842541.jpg" size="750x616"> <img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2JLvUsXXXXXXLXXXXXXXXXXXX_!!849842541.jpg" size="750x783"><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2qMfusXXXXXc1XXXXXXXXXXXX_!!849842541.gif" size="750x609"><img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB2c56gsXXXXXakXpXXXXXXXXXX_!!849842541.gif" size="750x380"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2YD2PsXXXXXaaXXXXXXXXXXXX_!!849842541.gif" size="750x389"><img align="absmiddle" src="//img.alicdn.com/imgextra/i3/849842541/TB2iiYMsXXXXXaKXXXXXXXXXXXX_!!849842541.gif" size="750x372"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2v_PzsXXXXXb0XXXXXXXXXXXX_!!849842541.gif" size="750x625"><img align="absmiddle" src="//img.alicdn.com/imgextra/i3/849842541/TB2UTKMbVHzQeBjSZFHXXbwZpXa_!!849842541.jpg" size="750x650"> <img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB2vRjEsXXXXXbtXXXXXXXXXXXX_!!849842541.jpg" size="750x820"><img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB22.C5sXXXXXczXpXXXXXXXXXX_!!849842541.jpg" size="750x1350"><img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB2c5YPsXXXXXamXXXXXXXXXXXX_!!849842541.jpg" size="750x1350"><img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB2nwzLsXXXXXaWXXXXXXXXXXXX_!!849842541.gif" size="750x639"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2JfS1sXXXXXXDXFXXXXXXXXXX_!!849842541.gif" size="750x423"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2XbHIsXXXXXaEXXXXXXXXXXXX_!!849842541.gif" size="750x475"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2bzfvsXXXXXcFXXXXXXXXXXXX_!!849842541.gif" size="750x441"><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB20EDGsXXXXXbsXXXXXXXXXXXX_!!849842541.gif" size="750x423"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2sD_tsXXXXXcUXXXXXXXXXXXX_!!849842541.gif" size="750x418"><img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB2Gf12sVXXXXajXXXXXXXXXXXX_!!849842541.gif" size="750x900"><img align="absmiddle" src="//img.alicdn.com/imgextra/i3/849842541/TB2p0W1sVXXXXXLXXXXXXXXXXXX_!!849842541.gif" size="750x848"><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2JwiZsVXXXXbhXXXXXXXXXXXX_!!849842541.gif" size="750x792"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB20m1Pb8LzQeBjSZFjXXcscpXa_!!849842541.jpg" size="750x820"><img align="absmiddle" src="//img.alicdn.com/imgextra/i3/849842541/TB27kKcahwb61BjSZFlXXbuoVXa_!!849842541.jpg" size="750x820"> <img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2cipClpXXXXcHXXXXXXXXXXXX_!!849842541.jpg" size="750x754"><img align="absmiddle" src="//img.alicdn.com/imgextra/i3/849842541/TB2LxYasVXXXXX4XXXXXXXXXXXX_!!849842541.jpg" size="750x597"><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2tfYgsVXXXXXdXXXXXXXXXXXX_!!849842541.jpg" size="750x901"><img align="absmiddle" src="//img.alicdn.com/imgextra/i3/849842541/TB2liVilpXXXXbFXpXXXXXXXXXX_!!849842541.jpg" size="750x1000"><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2ycFGlpXXXXb7XXXXXXXXXXXX_!!849842541.jpg" size="750x750"><img align="absmiddle" src="//img.alicdn.com/imgextra/i2/849842541/TB2fYxQlpXXXXaFXXXXXXXXXXXX_!!849842541.jpg" size="750x750"><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2JVtjlpXXXXbGXpXXXXXXXXXX_!!849842541.jpg" size="750x750"><img align="absmiddle" src="//img.alicdn.com/imgextra/i3/849842541/TB2e7NQlpXXXXarXXXXXXXXXXXX_!!849842541.jpg" size="750x750"><img align="absmiddle" src="//img.alicdn.com/imgextra/i4/849842541/TB2__JzlpXXXXcRXXXXXXXXXXXX_!!849842541.jpg" size="750x750"><img align="absmiddle" src="//img.alicdn.com/imgextra/i1/849842541/TB2kyBClpXXXXcDXXXXXXXXXXXX_!!849842541.jpg" size="750x784" /></p>';
        echo '<pre>';
        $arr = explode('<img',$json);
        $reg = '/[\s\S]*?src\s*=\s*[\"|\'](.*?jpg)[\"|\'][\s\S]*?[\s\S]*?size\s*=\s*[\"|\'](\d+)x(\d+)[\"|\'][\s\S]*?/';
        $json_arr = [];
        foreach($arr as $val){

            $matches = [];
            preg_match_all($reg,$val, $matches);
            if($matches[1] && $matches[2] && $matches[3]){
                $json_arr[] = [
                    'url' => 'https:'.$matches[1][0].'_400x400q90.jpg',
                    'size' => [
                        'w' => $matches[2][0],
                        'h' => $matches[3][0]
                    ]
                ];
            }

        }
        echo json_encode($json_arr);

        die;
    }


    private function log($content='') {
        $fp = fopen('/tmp/test_book.log','a');
        if(!$fp){
            return ;
        }
        fwrite($fp, $content);
        fclose($fp);
    }


}
