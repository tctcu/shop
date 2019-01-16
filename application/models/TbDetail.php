<?php

#商品详情表
class TbDetailModel extends MysqlModel
{
    protected $_name = 'tb_detail';

    function __construct()
    {
        parent::__construct();
    }

    #添加
    function addData($data)
    {
        if (empty($data)) {
            return false;
        }
        $data['created_at'] = time();
        return $this->insert($data);
    }

    #查找单条信息
    function getDataByItemId($itemid = 0)
    {
        if (empty($itemid)) {
            return false;
        }
        $where = $this->_db->quoteInto('itemid = ?', $itemid);
        $data = $this->fetchRow($where);
        if (!empty($data)) {
            return $data->toArray();
        }
        return false;
    }


    function makeDetail($info)
    {
        return [
            'itemid' => $info['itemid'],
            'itemshorttitle' => $info['itemshorttitle'],
            'itemprice' => $info['itemprice'],
            'itemsale' => $info['itemsale'],
            'itempic' => $info['itempic'] . '_310x310.jpg',
            'itemendprice' => $info['itemendprice'],
            'coupon_type' => $info['coupon_type'] ? $info['coupon_type'] : '0',//优惠券状态 0-没有券
            'couponmoney' => $info['couponmoney'],
            'couponexplain' => $info['couponexplain'],
            'couponstarttime' => $info['couponstarttime'],
            'couponendtime' => $info['couponendtime'],
            'shoptype' => $info['shoptype'],
            'rebate' => sprintf("%.2f", $info['tkrates'] * ConfigModel::RATE * $info['itemendprice'] * ConfigModel::REBATE),
            'taobao_image' => $info['taobao_image'] ? explode(',', $info['taobao_image']) : [],
            'taobao_detail' => $info['taobao_detail'] ? explode(',', $info['taobao_detail']) : [],
            'shopname' => $info['shopname'],
            'detail_json_url' => 'https://h5api.m.taobao.com/h5/mtop.taobao.detail.getdesc/6.0/?data={%22id%22:%22' . $info['itemid'] . '%22}',
            'share' => [
                'share_title' => $info['itemshorttitle'] . '到手价￥' . $info['itemprice'],
                'share_pic' => $info['itempic'] . '_150x150.jpg',
                'share_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/Web/shareDetail?itemid=' . $info['itemid'] . '&tkl=',
                'share_tpwd' => $info['itemshorttitle'] . "
                --------
                【在售价】". $info['itemprice'] ."元
                【到手价】". $info['itemendprice'] ."元
                --------
                復·制这段描述【tpwd】
                咑閞淘♂寳♀即可查看"
            ]
        ];
    }
}