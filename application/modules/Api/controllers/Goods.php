<?php

#商品
class GoodsController extends ApiController
{
    private $pid = 'mm_234440039_166200410_57891600477';//'mm_116356778_18618211_65740777';

    function init()
    {
        parent::init();
    }

    #列表
    function listAction()
    {
        $sort = intval($_REQUEST['sort']) ? intval($_REQUEST['sort']) : 9;
        $cid = intval($_REQUEST['cid']) ? intval($_REQUEST['cid']) : 0;
        $min_id = intval($_REQUEST['min_id']) ? intval($_REQUEST['min_id']) : 1;
        $pageSize = intval($_REQUEST['pageSize']) ? intval($_REQUEST['pageSize']) : 20;
        $url = "http://v2.api.haodanku.com/itemlist/apikey/allfree/nav/3/cid/" . $cid . "/back/" . $pageSize . "/min_id/" . $min_id . "/sort/" . $sort;
        $json = file_get_contents($url);
        $ret_data = json_decode($json, true);
        $data = array(
            'min_id' => $ret_data['min_id'] . ''
        );
        $data['list'] = $this->make($ret_data['data']);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #详情
    function detailAction()
    {
        $itemid = intval($_REQUEST['itemid']);
        $url = "http://v2.api.haodanku.com/item_detail/apikey/allfree/itemid/" . $itemid;
        $json = file_get_contents($url);
        $val = json_decode($json, true)['data'];

        $data = array(
            'itemid' => $val['itemid'],
            'itemshorttitle' => $val['itemshorttitle'],
            'itemdesc' => $val['itemdesc'],
            'itemprice' => $val['itemprice'],
            'itemsale' => $val['itemsale'],
            'itempic' => $val['itempic'] . '_310x310.jpg',
            'itemendprice' => $val['itemendprice'],
            'url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $val['activityid'] . '&itemId=' . $val['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid,
            'couponnum' => $val['couponnum'],
            'couponreceive2' => $val['couponreceive2'],
            'couponmoney' => $val['couponmoney'],
            'couponexplain' => $val['couponexplain'],
            'couponstarttime' => $val['couponstarttime'],
            'couponendtime' => $val['couponendtime'],
            'shoptype' => $val['shoptype'],
            'taobao_image' => explode(',', $val['taobao_image']),
            'itempic_copy' => 'http://img.haodanku.com/' . $val['itempic_copy'] . '-600',
            'fqcat' => $val['fqcat'],
            'sellernick' => $val['sellernick'],
            'discount' => $val['discount'],
            'activity_type' => $val['activity_type'],
            'video_url' => $val['videoid'] ? 'http://cloud.video.taobao.com/play/u/1/p/1/e/6/t/1/' . $val['videoid'] . 'mp4' : '',
            'share' => array(
                'share_title' => $val['itemshorttitle'] . '  领券后￥' . $val['itemprice'],
                'share_pic' => 'http://img.haodanku.com/' . $val['itempic_copy'] . '-100',
                'share_url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $val['activityid'] . '&itemId=' . $val['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid
            ),

        );

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #分类
    function categoryAction()
    {
        $url = "http://v2.api.haodanku.com/super_classify/apikey/allfree";
        $json = file_get_contents($url);
        $ret_data = json_decode($json, true)['general_classify'];

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $ret_data);
    }

    #热搜词
    function keywordAction()
    {
        $url = "http://v2.api.haodanku.com/hot_key/apikey/allfree";
        $json = file_get_contents($url);
        $ret_data = array_slice(json_decode($json, true)['data'], 0, 20);
        foreach ($ret_data as &$val) {
            $val['emoji'] = '';
            $val['color'] = '#212121';
        }
        $ret_data[0]['emoji'] = "\xF0\x9F\x94\xA5";
        $ret_data[0]['color'] = '#FF3030';
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $ret_data);
    }

    #关键词搜索
    function searchAction()
    {
        $cid = intval($_REQUEST['cid']);
        $keyword = trim($_REQUEST['keyword']);
        $min_id = intval($_REQUEST['min_id']);
        $url = "http://v2.api.haodanku.com/get_keyword_items/apikey/allfree/keyword/" . urlencode(urlencode($keyword)) . "/back/20/sort/0/cid/" . $cid;
        if ($min_id) {
            $url .= "/min_id/" . $min_id;
        }
        $json = file_get_contents($url);
        $ret_data = json_decode($json, true);
        $data = array(
            'min_id' => $ret_data['min_id'] . ''
        );
        $data['list'] = $this->make($ret_data['data']);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }


    #单品关联推荐
    function recommendAction()
    {
        $itemid = intval($_REQUEST['itemid']);
        $url = "http://v2.api.haodanku.com/get_similar_info/apikey/allfree/itemid/" . $itemid;
        $json = file_get_contents($url);
        $ret_data = json_decode($json, true);

        $data = array();
        $data['list'] = $this->make($ret_data['data']);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }


    #快抢
    function fastBuyAction()
    {
        $hour_type = intval($_REQUEST['hour_type']) ? intval($_REQUEST['hour_type']) : 7;
        $min_id = intval($_REQUEST['min_id']) ? intval($_REQUEST['min_id']) : 1;
        $url = "http://v2.api.haodanku.com/fastbuy/apikey/allfree/hour_type/" . $hour_type . "/min_id/" . $min_id;
        $json = file_get_contents($url);
        $ret_data = json_decode($json, true);

        $data = array();
        foreach ($ret_data['data'] as $val) {
            #券链接 获取 券ID
            $arr = explode('&', parse_url($val['couponurl'])['query']);
            $params = array();
            foreach ($arr as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
            }
            #处理详情
            $content = json_decode($val['material_info']['seckill_content'], true);
            $content_info = [];
            foreach ($content as $v) {
                $content_info[] = [
                    'img' => 'http://img.haodanku.com/' . $v['img'] . '-600',
                    'text' => $v['text']
                ];
            }

            $data['list'][] = array(
                'itemid' => $val['itemid'],
                'itemshorttitle' => $val['itemshorttitle'],
                'itemdesc' => $val['itemdesc'],
                'itemprice' => $val['itemprice'],
                'itemsale' => $val['itemsale'],
                'itempic' => $val['itempic'],
                'itemendprice' => $val['itemendprice'],
                'url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $params['activityId'] . '&itemId=' . $val['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid,
                'couponmoney' => $val['couponmoney'],
                'couponexplain' => '',
                'couponstarttime' => '',
                'couponendtime' => '',
                'shoptype' => $val['shoptype'],
                'grab_type' => $val['grab_type'],
                'start_time' => $val['start_time'],
                'short_itemdesc' => $val['short_itemdesc'],
                'material_info' => [
                    'seckill_content' => $content_info,
                    'main_video_url' => $val['material_info']['main_video_url'],
                    'detail_video_url' => $val['material_info']['detail_video_url']
                ],

            );
        }

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #专题
    function subjectAction()
    {
        $url = "http://v2.api.haodanku.com/get_subject/apikey/allfree";
        $json = file_get_contents($url);
        $ret_data = json_decode($json, true);
        $data = array();
        foreach ($ret_data['data'] as $val) {
            $data['list'][] = array(
                'id' => $val['id'],
                'name' => $val['name'],
                'app_image' => 'http://img.haodanku.com/' . $val['app_image'] . '-600',
                'content' => $val['content'],
                'activity_start_time' => $val['activity_start_time'],
                'activity_end_time' => $val['activity_end_time']
            );
        }

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #专题关联单品
    function subjectItemAction()
    {
        $id = intval($_REQUEST['id']) ? intval($_REQUEST['id']) : 1966;
        $url = "http://v2.api.haodanku.com/get_subject_item/apikey/allfree/id/" . $id;
        $json = file_get_contents($url);
        $ret_data = json_decode($json, true);
        $data = array();
        $data['list'] = $this->make($ret_data['data']);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #识别淘口令
    function convertAction(){
        $content = addslashes(htmlspecialchars(trim($_REQUEST['content'])));
        if(empty($content)){
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
        }

        $taobao_model = new TaobaoModel();
        $yuque_model = new YuQueModel();
        $item_id = $this->quid($content);//链接
        if(empty($item_id)) {
            if (preg_match('#\x{ffe5}([a-zA-Z0-9]{11})\x{ffe5}#isu', $content, $m)) {//淘口令
                $condition = [
                    'password_content' => $m[0]
                ];
                $item_id = $yuque_model->tpwdConvert($condition);
            } else if(mb_strlen($content)>10){//标题
                $condition = [
                    'keyword' => $content
                ];
                $item_info = $taobao_model->TbkItemGetRequest($condition);
                $item_id = $item_info['num_iid'];
            }
        }

        $data = [];
        if($item_id) {
            $condition = [
                $item_id
            ];
            $item_info = $taobao_model->TbkItemInfoGetRequest($condition);
            $condition = [
                'item_id' => $item_id
            ];
            $url_info = $yuque_model->privilegeGet($condition);
            $data = $this->makeTb($item_info,$url_info);
        }

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #链接获取淘宝ID
    private function quid($strurl) {
        $NO = 0;
        $strurl = strtolower ( $strurl );
        if (strpos ( $strurl, 'id' ) !== false) {
            $arr = explode ( '?', $strurl );
            $arr = explode ( '&', $arr [1] );
            foreach ( $arr as $k => $v ) {
                if (is_string ( $v )) {
                    //判断是否含有id
                    if (strpos ( $v, 'id' ) !== false) {
                        //处理含有item或者num项 返还id数
                        if (strpos ( $v, 'item' ) !== false || strpos ( $v, 'num' ) !== false) {
                            $i = strrpos ( $v, '=' );
                            $str = substr ( $v, $i + 1 );
                            if (is_numeric ( $str )) {
                                $NO = $str;
                            }
                        } else {
                            $i = strrpos ( $v, '=' );
                            $str = substr ( $v, $i + 1 );
                            $x = strlen ( $str );
                            if (is_numeric ( $str )) {
                                if ($x ==11) {
                                    $NO = $str;
                                } else if ($NO == 0 || ($x > 9 && $x < 11)) {
                                    $NO = $str;
                                }
                            }
                        }
                    }
                }
            }

        }
        return $NO;
    }

    #格式化淘宝数据
    private function makeTb($item_info,$url_info)
    {
        $data = [
            'itemid' => $item_info['num_iid'].'',
            'itemshorttitle' => $item_info['title'],
            'itemdesc' => $item_info['title'],
            'itemprice' => $item_info['zk_final_price'].'',
            'itemsale' => $item_info['volume'].'',
            'itempic' => $item_info['pict_url'],
            'itemendprice' => $item_info['zk_final_price'],
            'url' => $url_info['item_url'],
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
            $data['itemendprice'] = ($data['itemendprice']-$couponmoney).'';
            $data['url'] = $url_info['coupon_click_url'];
            $data['couponmoney'] = $couponmoney.'';
            $data['couponexplain'] = $url_info['coupon_info'];
            $data['couponstarttime'] = strtotime($url_info['coupon_start_time']).'';
            $data['couponendtime'] = strtotime($url_info['coupon_end_time']).'';
        }
        return $data;
    }



    #格式化列表数据
    private function make($data)
    {
        $list = [];
        foreach ($data as $val) {
            $list[] = array(
                'itemid' => $val['itemid'],
                'itemshorttitle' => $val['itemshorttitle'],
                'itemdesc' => $val['itemdesc'],
                'itemprice' => $val['itemprice'],
                'itemsale' => $val['itemsale'],
                'itempic' => $val['itempic'],
                'itemendprice' => $val['itemendprice'],
                'url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $val['activityid'] . '&itemId=' . $val['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid,
                'couponmoney' => $val['couponmoney'],
                'couponexplain' => '',
                'couponstarttime' => $val['couponstarttime'],
                'couponendtime' => $val['couponendtime'],
                'shoptype' => $val['shoptype'],
                //'taobao_image' => explode(',' ,$val['taobao_image']),
            );
        }
        return $list;
    }
}