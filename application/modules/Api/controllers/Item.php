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
        $max_price = intval($_REQUEST['max_price']) ? intval($_REQUEST['max_price']) : 0;
        $pageSize = intval($_REQUEST['pageSize']) ? intval($_REQUEST['pageSize']) : 20;

        $condition = [
            'status' => 1,
            'fqcat' => $cid,
            'max_price' => $max_price,
            'min_id' => $min_id,
        ];
        if($cid<0){
            $condition['shopname'] = '天猫超市';
            unset($condition['fqcat']);
        }

        $tb_model = new TbModel();
        $tb_list = $tb_model->getList($pageSize,$condition);
        $data = $tb_model->makeList($tb_list);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #详情
    function detailAction()
    {
        $itemid = intval($_REQUEST['itemid']);
        if(empty($itemid)){
            $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
        }
        $url = "http://v2.api.haodanku.com/item_detail/apikey/allfree/itemid/" . $itemid;
        $json = file_get_contents($url);
        $tb_info = json_decode($json, true)['data'];
        $taobao_model = new TaobaoModel();

        if(empty($tb_info)){//查库
            $tb_model = new TbModel();
            $tb_info = $tb_model->getDataByItemId($itemid);

            if($tb_info['status']<>1 || $tb_info['couponendtime']<=time()){//过滤失效的商品
                $tb_info = [];
            }
        } else {
            if($tb_info['couponendtime']<=time() || $tb_info['end_time']<=time() || $tb_info['report_status'] == 3){//过滤失效的商品
                $tb_info = [];
            }
        }
        if($tb_info['itemid'] && empty($tb_info['taobao_image'])){//淘宝图片
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
            if(empty($url_info)){
                $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG);
            }
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




    #客户端维护淘宝详情页
    function updateDetailAction(){
        $itemid = intval($_REQUEST['itemid']);
        $detail_arr = json_decode(trim($_REQUEST['detail_json']),true);

        $taobao_detail = [];
        if($itemid && $detail_arr['data']['pcDescContent']){
            $arr = explode('<img',$detail_arr['data']['pcDescContent']);
            $reg_1 = '/[\s\S]*?src\s*=\s*[\"|\'](.*?jpg)[\"|\'][\s\S]*?[\s\S]*?size\s*=\s*[\"|\'](\d+)x(\d+)[\"|\'][\s\S]*?/';
            $reg_2 = '/[\s\S]*?src\s*=\s*[\"|\'](.*?jpg)[\"|\'][\s\S]*?[\s\S]*?width\s*=\s*[\"|\'](\d+)[\"|\'][\s\S]*?height\s*=\s*[\"|\'](\d+)[\"|\'][\s\S]*?/';

            foreach($arr as $val) {
                $matches = [];
                preg_match_all($reg_1, $val, $matches);
                if ($matches[1] && $matches[2] && $matches[3]) {
                    $taobao_detail[] = [
                        'url' => 'https:' . $matches[1][0] . '_q90.jpg',
                        'size' => [
                            'w' => $matches[2][0],
                            'h' => $matches[3][0]
                        ]
                    ];

                } else {
                    preg_match_all($reg_2, $val, $matches);
                    if ($matches[1] && $matches[2] && $matches[3]) {
                        $taobao_detail[] = [
                            'url' => 'https:' . $matches[1][0] . '_q90.jpg',
                            'size' => [
                                'w' => $matches[2][0],
                                'h' => $matches[3][0]
                            ]
                        ];
                    }

                }
            }

            if($taobao_detail) {
                $tb_detail_model = new TbDetailModel();
                $add = [
                    'itemid' => $itemid,
                    'taobao_detail' => json_encode($taobao_detail),
                ];
                try {
                    $tb_detail_model->addData($add);
                } catch (Exception $ex) {

                }
            }
        }

        $data = [
            'taobao_detail' => $taobao_detail
        ];
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

}