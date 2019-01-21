<?php
class WebController extends Yaf_Controller_Abstract
{
    private $pid = 'mm_234440039_166200410_57891600477';//'mm_116356778_18618211_65740777';

    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    #协议
    function protocolAction(){}
    #引导识别淘口令
    function courseAction(){}
    #返利规则
    function rebateRuleAction(){}
    #淘宝分享详情
    function shareDetailAction(){
        $itemid =  intval($_REQUEST['itemid']);
        $tkl =  trim($_REQUEST['tkl']);
        //$tkl =  '￥miWFbqdOWkc￥';
        if(empty($itemid)){
            echo 404;die;
        }
        $url = "http://v2.api.haodanku.com/item_detail/apikey/allfree/itemid/" . $itemid;
        $json = file_get_contents($url);
        $tb_info = json_decode($json, true)['data'];
        $taobao_model = new TaobaoModel();

        if(empty($tb_info)){//查库
            $tb_model = new TbModel();
            $tb_info = $tb_model->getDataByItemId($itemid);
        }
        if(empty($tb_info['taobao_image'])){//淘宝图片
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

        $data['url'] = 'http://uland.taobao.com/coupon/edetail?activityId=' . $tb_info['activityid'] . '&itemId=' . $tb_info['itemid'] . '&src=qmmf_sqrb&mt=1&pid=' . $this->pid;

        if($tkl){
            $tbk_info = json_decode(file_get_contents(ConfigModel::TKL_URL.$tkl),true);
            $data['url'] = $tbk_info['url'];
        }

        $this->_view->tb_info = $data;
    }


    #拼多多回调
    function pddAction(){
        echo  'success';die;
    }


}
