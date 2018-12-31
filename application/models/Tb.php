<?php
#商品表
class TbModel extends MysqlModel {
    protected $_name = 'tb';
    private $pid = 'mm_234440039_166200410_57891600477';//'mm_116356778_18618211_65740777';

    function __construct(){
        parent::__construct();
    }

    #添加
    function addData($data){
        if(empty($data)){
            return false;
        }
        $data['created_at'] = time();
        $data['updated_at'] = time();
        return $this->insert($data);
    }

    #删除
    function deleteData($id){
        if(empty($id)){
            return false;
        }
        return $this->delete("id = {$id}");
    }

    #更新
    function updateData($data, $id){
        if(empty($data) || empty($id)){
            return false;
        }
        $data['updated_at'] = time();
        return $this->update($data,"id = {$id}");
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

    function getListData($page_size =  20,$condition = array()){
        $sql = " select * from {$this->_name} where 1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
        }
        if(!empty($condition['status'])){
            $sql .= " and status={$condition['status']} ";
        }
        if(!empty($condition['fqcat'])){
            $sql .= " and fqcat={$condition['fqcat']} ";
        }

        if(!empty($condition['min_id']) && $condition['min_id']>1){
            $sql .= " and min_id<{$condition['min_id']} ";
        }

        $sql .= " order by min_id desc ";

        $sql .= " limit {$page_size}";

        try{
            $data = $this->_db->fetchAll($sql);
        }catch(Exception $ex){
            $data = array();
        }
        return $data;
    }

    function getListCount($condition = array()){
        $sql = " select count(*) as num from {$this->_name} where 1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
        }
        if(!empty($condition['status'])){
            $sql .= " and status={$condition['status']} ";
        }

        $result = $this->_db->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }

    function makeList($list){
        $data = [];
        foreach($list as $key=>$item){
            if(empty($data['min_id']) || $item['min_id'] < $data['min_id']){
                $data['min_id'] = $item['min_id'];
            }
            $data['list'][] = [
                'itemid' => $item['itemid'],
                'itemshorttitle' => $item['itemshorttitle'],
                'itemdesc' => $item['itemdesc'],
                'itemprice' => $item['itemprice'],
                'itemsale' => $item['itemsale'],
                'itempic' => $item['itempic'],
                'itemendprice' => $item['itemendprice'],
                'couponmoney' => $item['couponmoney'],
                'couponexplain' => $item['couponexplain'],
                'couponstarttime' => $item['couponstarttime'],
                'couponendtime' => $item['couponendtime'],
                'shoptype' => $item['shoptype'],
                'rebate' => sprintf("%.2f",$item['tkrates']*0.005*$item['itemendprice'])
            ];
        }
        return $data;
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
            'rebate' => sprintf("%.2f",$info['tkrates']*0.005*$info['itemendprice']),
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