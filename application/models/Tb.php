<?php
#商品表
class TbModel extends MysqlModel {
    protected $_name = 'tb';
    private $pid = 'mm_116356778_18618211_65740777';//'mm_234440039_166200410_57891600477';
    const RATE = '0.01';//换算佣金比例%
    const REBATE = '0.5';//返利比例
    const MEMBER = [//对应config
        '234440039' => 1,
        '116356778' => 2,
    ];

    const CATEGORY = [
        [
            'cid' => '0',
            'name' => '精选'
        ],
        [
            'cid' => '1',
            'name' => '女装'
        ],
        [
            'cid' => '2',
            'name' => '男装'
        ],
        [
            'cid' => '3',
            'name' => '内衣'
        ],
        [
            'cid' => '4',
            'name' => '美妆'
        ],
        [
            'cid' => '5',
            'name' => '配饰'
        ],
        [
            'cid' => '6',
            'name' => '鞋品'
        ],
        [
            'cid' => '7',
            'name' => '箱包'
        ],
        [
            'cid' => '8',
            'name' => '儿童'
        ],
        [
            'cid' => '9',
            'name' => '母婴'
        ],
        [
            'cid' => '10',
            'name' => '居家'
        ],
        [
            'cid' => '11',
            'name' => '美食'
        ],
        [
            'cid' => '12',
            'name' => '数码'
        ],
        [
            'cid' => '13',
            'name' => '家电'
        ],
        [
            'cid' => '15',
            'name' => '车品'
        ],
        [
            'cid' => '16',
            'name' => '文体'
        ],
        [
            'cid' => '14',
            'name' => '其他'
        ]
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

    function getList($page_size =  20,$condition = array()){
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
        if(!empty($condition['max_price']) && $condition['max_price']>0){
            $sql .= " and itemendprice <= {$condition['max_price']} ";
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

    function getListData($page = 1,$page_size =  20,$condition = array()){
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
        if(!empty($condition['itemid'])){
            $sql .= " and itemid={$condition['itemid']} ";
        }
        if(!empty($condition['itemshorttitle'])){
            $sql .= " and itemshorttitle like '%{$condition['itemshorttitle']}%' ";
        }

        $sql .= " order by id desc ";

        $start = ($page -1 ) * $page_size;
        $sql .= " limit {$start}, {$page_size}";

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
        if(!empty($condition['fqcat'])){
            $sql .= " and fqcat={$condition['fqcat']} ";
        }
        if(!empty($condition['itemid'])){
            $sql .= " and itemid={$condition['itemid']} ";
        }
        if(!empty($condition['itemshorttitle'])){
            $sql .= " and itemshorttitle like '%{$condition['itemshorttitle']}%' ";
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
            $data['list'][] = $this->makeItem($item);
        }
        return $data;
    }

    function makeItem($item){
        return [
            'itemid' => $item['itemid'],
            'itemshorttitle' => $item['itemshorttitle'],
            'itemdesc' => $item['itemdesc'],
            'itemprice' => $item['itemprice'],
            'itemsale' => $item['itemsale'],
            'itempic' => $item['itempic'],
            'itemendprice' => $item['itemendprice'],
            'url' => 'http://uland.taobao.com/coupon/edetail?activityId=' . $item['activityid'] . '&itemId=' . $item['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid,
            'coupon_type' => '1',//优惠券状态 0-没有券 好单库的都有券
            'couponmoney' => $item['couponmoney'],
            'couponexplain' => $item['couponexplain'],
            'couponstarttime' => $item['couponstarttime'],
            'couponendtime' => $item['couponendtime'],
            'shoptype' => $item['shoptype'],
            'rebate' => sprintf("%.2f",$item['tkrates'] * ConfigModel::RATE * $item['itemendprice'] * ConfigModel::REBATE)
        ];
    }
}