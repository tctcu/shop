<?php
#商品表
class TbModel extends MysqlModel {
    protected $_name = 'tb';
    private $pid = 'mm_234440039_166200410_57891600477';//'mm_116356778_18618211_65740777';
    const REBATE = '0.005';//返利比例
    const MEMBER = [//对应config
        '234440039' => 1,
        '116356778' => 2,
    ];

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
                'rebate' => sprintf("%.2f",$item['tkrates'] * TbModel::REBATE * $item['itemendprice'])
            ];
        }
        return $data;
    }
}