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
        $tb_model = new TbModel();
        $tb_info = $tb_model->getDataByItemId($itemid);
        $data = $tb_model->makeDetail($tb_info);

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

}