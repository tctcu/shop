<?php
#商品详情表
class TbDetailModel extends MysqlModel {
    protected $_name = 'tb_detail';

    function __construct(){
        parent::__construct();
    }

    #添加
    function addData($data){
        if(empty($data)){
            return false;
        }
        $data['created_at'] = time();
        return $this->insert($data);
    }

    #查找单条信息
    function getDataByItemId($itemid = 0){
        if(empty($itemid)){
            return false;
        }
        $where = $this->_db->quoteInto('itemid = ?',$itemid);
        $data = $this->fetchRow($where);
        if(!empty($data)){
            return $data->toArray();
        }
        return false;
    }


    function makeDetail($info){
        return[
            'itemid' => $info['itemid'],
            'itemshorttitle' => $info['itemshorttitle'],
            'itemdesc' => $info['itemdesc'],
            'itemprice' => $info['itemprice'],
            'itemsale' => $info['itemsale'],
            'itempic' => $info['itempic'] . '_310x310.jpg',
            'itemendprice' => $info['itemendprice'],
            'url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $info['activityid'] . '&itemId=' . $info['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid,
            'couponnum' => $info['couponnum'],
            'couponreceive' => $info['couponreceive'],
            'couponmoney' => $info['couponmoney'],
            'couponexplain' => $info['couponexplain'],
            'couponstarttime' => $info['couponstarttime'],
            'couponendtime' => $info['couponendtime'],
            'shoptype' => $info['shoptype'],
            'rebate' => sprintf("%.2f",$info['tkrates'] * TbModel::REBATE * $info['itemendprice']),
            'taobao_image' => $info['taobao_image'] ? explode(',', $info['taobao_image']) : [],
            'taobao_detail' =>$info['taobao_detail'] ? explode(',', $info['taobao_detail']) : [],
            'itempic_copy' => 'http://img.haodanku.com/' . $info['itempic_copy'] . '-600',
            'fqcat' => $info['fqcat'],
            'shopname' => $info['shopname'],
            'video_url' => $info['videoid'] ? 'http://cloud.video.taobao.com/play/u/1/p/1/e/6/t/1/' . $info['videoid'] . 'mp4' : '',
            'share' => array(
                'share_title' => $info['itemshorttitle'] . '  领券后￥' . $info['itemprice'],
                'share_pic' => 'http://img.haodanku.com/' . $info['itempic_copy'] . '-100',
                'share_url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $info['activityid'] . '&itemId=' . $info['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid
            )
        ];
    }
}