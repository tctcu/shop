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
        }
        $tb_detail_model = new TbDetailModel();
        $tb_detail_info = $tb_detail_model->getDataByItemId($itemid);
        $tb_info['taobao_detail'] = $tb_detail_info['taobao_detail'];
        $data = $tb_detail_model->makeDetail($tb_info);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

}