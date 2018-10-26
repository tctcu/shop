<?php
#商品
class GoodsController extends ApiController
{

    function init()
    {
        parent::init();
    }

    #列表
    function listAction()
    {
        $sort = intval($_REQUEST['sort']) ? intval($_REQUEST['sort']) : 0;
        $cid = intval($_REQUEST['cid']) ? intval($_REQUEST['cid']) : 0;
        $min_id = intval($_REQUEST['min_id']) ? intval($_REQUEST['min_id']) : 1;
        $pageSize = intval($_REQUEST['pageSize']) ? intval($_REQUEST['pageSize']) : 20;
        $url = "http://v2.api.haodanku.com/itemlist/apikey/allfree/nav/3/cid/".$cid."/back/".$pageSize."/min_id/".$min_id."/sort/".$sort;
        $json = file_get_contents($url);
        //var_dump($json);die;
        $ret_data = json_decode($json,true);
        $data = array(
            'min_id' => $ret_data['min_id']
        );
        foreach($ret_data['data'] as $val){
            $data['list'][] = array(
                'itemid' => $val['itemid'],
                'itemshorttitle' => $val['itemshorttitle'],
                'itemdesc' => $val['itemdesc'],
                'itemprice' => $val['itemprice'],
                'itemsale' => $val['itemsale'],
                'itempic' => $val['itempic'],
                'itemendprice' => $val['itemendprice'],
                'url' => 'http://uland.taobao.com/coupon/edetail?activityId='.$val['activityid'].'&itemId='.$val['itemid'].'&src=qmmf_sqrb&mt=1&pid=mm_116356778_18618211_65740777',
                'couponmoney' => $val['couponmoney'],
                'couponexplain' => $val['couponexplain'],
                'couponstarttime' => $val['couponstarttime'],
                'couponendtime' => $val['couponendtime'],
                'shoptype' => $val['shoptype'],
//                'taobao_image' => explode(',' ,$val['taobao_image']),
            );
        }
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #详情
    function detailAction()
    {
        $itemid = intval($_REQUEST['itemid']);
        $url = "http://v2.api.haodanku.com/item_detail/apikey/allfree/itemid/".$itemid;
        $json = file_get_contents($url);
        $val = json_decode($json,true)['data'];

        $data = array(
            'itemid' => $val['itemid'],
            'itemshorttitle' => $val['itemshorttitle'],
            'itemdesc' => $val['itemdesc'],
            'itemprice' => $val['itemprice'],
            'itemsale' => $val['itemsale'],
            'itempic' => $val['itempic'].'_310x310.jpg',
            'itemendprice' => $val['itemendprice'],
            'url' => 'http://uland.taobao.com/coupon/edetail?activityId='.$val['activityid'].'&itemId='.$val['itemid'].'&src=qmmf_sqrb&mt=1&pid=mm_116356778_18618211_65740777',
            'couponnum' => $val['couponnum'],
            'couponreceive2' => $val['couponreceive2'],
            'couponmoney' => $val['couponmoney'],
            'couponexplain' => $val['couponexplain'],
            'couponstarttime' => $val['couponstarttime'],
            'couponendtime' => $val['couponendtime'],
            'shoptype' => $val['shoptype'],
            'taobao_image' => explode(',' ,$val['taobao_image']),
            'itempic_copy' => 'http://img.haodanku.com/'.$val['itempic_copy'].'-600',
            'fqcat' => $val['fqcat'],
            'sellernick' => $val['sellernick'],
            'discount' => $val['discount'],
            'activity_type' => $val['activity_type'],
            'video_url' => $val['videoid']? 'http://cloud.video.taobao.com/play/u/1/p/1/e/6/t/1/'.$val['videoid'].'mp4' : '',
            'share' => array(
                'share_title' => $val['itemshorttitle'].'  领券后￥'.$val['itemprice'],
                'share_pic' =>  'http://img.haodanku.com/'.$val['itempic_copy'].'-100',
                'share_url' => 'http://uland.taobao.com/coupon/edetail?activityId='.$val['activityid'].'&itemId='.$val['itemid'].'&src=qmmf_sqrb&mt=1&pid=mm_116356778_18618211_65740777'
            ),

        );
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #分类
    function categoryAction(){
        $url = "http://v2.api.haodanku.com/super_classify/apikey/allfree";
        $json = file_get_contents($url);
        //print_r($json);die;
        $ret_data = json_decode($json,true)['general_classify'];
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $ret_data);
    }

    #热搜词
    function keywordAction(){
        $url = "http://v2.api.haodanku.com/hot_key/apikey/allfree";
        $json = file_get_contents($url);
        $ret_data = array_slice( json_decode($json,true)['data'],0,20);
        foreach($ret_data as &$val){
            $val['emoji'] = '';
            $val['color'] = '#212121';
        }
        $ret_data[0]['emoji'] = "\xF0\x9F\x94\xA5";
        $ret_data[0]['color'] = '#FF3030';
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $ret_data);
    }

    #关键词搜索
    function searchAction(){
        $cid = intval($_REQUEST['cid']);
        $keyword = trim($_REQUEST['keyword']);
        $url = "http://v2.api.haodanku.com/get_keyword_items/apikey/allfree/keyword/".urlencode(urlencode($keyword))."/back/50/sort/0/cid/".$cid;
        $json = file_get_contents($url);
        $ret_data = json_decode($json,true);
        $data = array();
        foreach($ret_data['data'] as $val){
            $data['list'][] = array(
                'itemid' => $val['itemid'],
                'itemshorttitle' => $val['itemshorttitle'],
                'itemdesc' => $val['itemdesc'],
                'itemprice' => $val['itemprice'],
                'itemsale' => $val['itemsale'],
                'itempic' => $val['itempic'],
                'itemendprice' => $val['itemendprice'],
                'url' => 'http://uland.taobao.com/coupon/edetail?activityId='.$val['activityid'].'&itemId='.$val['itemid'].'&src=qmmf_sqrb&mt=1&pid=mm_116356778_18618211_65740777',
                'couponmoney' => $val['couponmoney'],
                'couponexplain' => $val['couponexplain'],
                'couponstarttime' => $val['couponstarttime'],
                'couponendtime' => $val['couponendtime'],
                'shoptype' => $val['shoptype'],
//                'taobao_image' => explode(',' ,$val['taobao_image']),
            );
        }
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }



    #单品关联推荐
    function recommendAction(){
        $itemid = intval($_REQUEST['itemid']);
        $url = "http://v2.api.haodanku.com/get_similar_info/apikey/allfree/itemid/".$itemid;
        $json = file_get_contents($url);
        $ret_data = json_decode($json,true);

        $data = array();
        foreach($ret_data['data'] as $val){
            $data['list'][] = array(
                'itemid' => $val['itemid'],
                'itemshorttitle' => $val['itemshorttitle'],
                'itemdesc' => $val['itemdesc'],
                'itemprice' => $val['itemprice'],
                'itemsale' => $val['itemsale'],
                'itempic' => $val['itempic'],
                'itemendprice' => $val['itemendprice'],
                'url' => 'http://uland.taobao.com/coupon/edetail?activityId='.$val['activityid'].'&itemId='.$val['itemid'].'&src=qmmf_sqrb&mt=1&pid=mm_116356778_18618211_65740777',
                'couponmoney' => $val['couponmoney'],
                'couponexplain' => $val['couponexplain'],
                'couponstarttime' => $val['couponstarttime'],
                'couponendtime' => $val['couponendtime'],
                'shoptype' => $val['shoptype'],
//                'taobao_image' => explode(',' ,$val['taobao_image']),
            );
        }
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    function testAction(){
        $contract_course_data['total_used_course_hour'] = 34.58;
        $strategy['actual_period'] = '34.58';
        var_dump($contract_course_data['total_used_course_hour'] * 100);//die;
        var_dump($strategy['actual_period'] * 100);die;
        if($contract_course_data['total_used_course_hour'] * 100 <> $strategy['actual_period'] * 100){
            echo '1';die;
        }
        echo '2';die;
    }
}