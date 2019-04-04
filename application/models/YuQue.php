<?php
/**
 * 语雀
 */

class YuQueModel{
    private $session = '';
    private $site_id = '';
    private $adzone_id = '';


    public function __construct($type = 1){
        $yuque_config = Yaf_Registry::get("config")->get('taobao.account.'.$type);
        $common_model = new CommonModel();
        $common_info = $common_model->getDataByType('session',$type);

        $this->session = $common_info['value'];
        $this->site_id = $yuque_config->site_id;
        $this->adzone_id = $yuque_config->adzone_id;
    }

    private function curl($url,$data){
        $headers = array(
            "Content-type: application/json"
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时
        if(0 === strpos(strtolower($url), 'https')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);//从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result_json = curl_exec($ch);//CURLOPT_RETURNTRANSFER 不设置  curl_exec返回TRUE 设置  curl_exec返回json(此处) 失败都返回FALSE
        curl_close($ch);

        return  json_decode($result_json, true);
    }

    #淘口令换淘宝ID
    function tpwdConvert($condition = array()){
        $password_content = isset($condition['password_content']) ? trim($condition['password_content']) : '';//淘口令文案
        if(empty($password_content)){
            return array();
        }

        $resp = [
            'session' => $this->session,
            'password_content' => $password_content
        ];

        $url = 'http://gateway.kouss.com/tbpub/tpwdConvert';
        $resp = $this->curl($url,$resp);

        if(isset($resp['data']['num_iid']) && !empty($resp['data']['num_iid'])){
            $retData = $resp['data']['num_iid'];
        } else {
            $retData = array();
        }

        return $retData;
    }

    #转换高佣
    function privilegeGet($condition = array()){
        $item_id = isset($condition['item_id']) ? intval($condition['item_id']) : '';//淘ID
        $session = isset($condition['session']) ? trim($condition['session']) : $this->session;//广告位id
        $adzone_id = isset($condition['adzone_id']) ? intval($condition['adzone_id']) : $this->adzone_id;//广告位id
        $site_id = isset($condition['site_id']) ? intval($condition['site_id']) : $this->site_id;//媒体id
        if(empty($item_id) || empty($session) || empty($adzone_id) || empty($site_id)){
            return array();
        }

        $resp = [//目前账号没有高佣 用个人账号转高佣
            'session' => $session,
            'adzone_id' => $adzone_id,
            'site_id' => $site_id,
            'item_id' => $item_id
        ];

        $url = 'http://gateway.kouss.com/tbpub/privilegeGet';
        $resp = $this->curl($url,$resp);
        if(isset($resp['result']['data']) && !empty($resp['result']['data'])){
            $retData = $resp['result']['data'];
        } else {
            $retData = array();
        }

        return $retData;
    }

    #订单
    function orderGet($start){
        $resp = [
            'session' => $this->session,
            //'fields' => 'relation_id,special_id,tb_trade_parent_id,tb_trade_id,site_id,adzone_id,alipay_total_price,income_rate,pub_share_pre_fee,num_iid,item_title,item_num,create_time,tk_status',
            'fields' => 'relation_id,special_id,tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id,relation_id,tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id,special_id,click_time',
            'span' => '1200',//秒
            'page_size' => '100',
            'order_query_type' => 'create_time',
            'tk_status' => '1',
            'start_time' => $start,
            'infoext' => 1,
        ];

        $url = 'http://gateway.kouss.com/tbpub/orderGet';
        $resp = $this->curl($url,$resp);
        $retData = [];
        if (isset($resp['tbk_sc_order_get_response']['results'])) {
            if (isset($resp['tbk_sc_order_get_response']['results']['n_tbk_order']) && !empty($resp['tbk_sc_order_get_response']['results']['n_tbk_order'])) {
                $retData = $resp['tbk_sc_order_get_response']['results']['n_tbk_order'];
            }
        } else {
            return 'busy';
        }

        return $retData;
    }


}