<?php
/*
 * 微信模型
 *
 * */

class WechatModel
{
    public $token = '';
    public $appid = '';
    public $secret = '';
    public $enckey = '';


    #微信公众平台要求鉴权算法
    public function checkSignature($signature,$timestamp,$nonce){
        $tmpArr = array($this->token, $timestamp, $nonce);

        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function curl($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result_json = curl_exec($ch);
        curl_close($ch);
        return  json_decode($result_json, true);
    }

    public function get_curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result_json = curl_exec($ch);
        curl_close($ch);
        return  json_decode($result_json, true);
    }


    /***************************************************
     * XML消息模板包装
     ***************************************************/
    #回复文本消息格式
    public function transmitText($object, $content){
        $textTpl = '<xml>'
            . '<ToUserName><![CDATA[' . $object->FromUserName . ']]></ToUserName>'
            . '<FromUserName><![CDATA[' . $object->ToUserName . ']]></FromUserName>'
            . '<CreateTime>' . time() . '</CreateTime>'
            . '<MsgType><![CDATA[text]]></MsgType>'
            . '<Content><![CDATA[' . $content  . ']]></Content>'
            . '</xml>';
        return $textTpl;
    }

    #回复图片消息
    public function transmitPic($object, $mediaId){
        $textTpl = '<xml>'
            . '<ToUserName><![CDATA[' . $object->FromUserName . ']]></ToUserName>'
            . '<FromUserName><![CDATA[' . $object->ToUserName . ']]></FromUserName>'
            . '<CreateTime>' . time() . '</CreateTime>'
            . '<MsgType><![CDATA[image]]></MsgType>'
            . '<Image><MediaId><![CDATA[' . $mediaId .']]></MediaId></Image>'
            . '</xml>';
        return $textTpl;
    }

    #回复图文消息：目前最大支持10条消息
    public function transmitPicText($object, $aryItems){
        $textTpl = '<xml>'
            . '<ToUserName><![CDATA[' . $object->FromUserName . ']]></ToUserName>'
            . '<FromUserName><![CDATA[' . $object->ToUserName . ']]></FromUserName>'
            . '<CreateTime>' . time() . '</CreateTime>'
            . '<MsgType><![CDATA[news]]></MsgType>'
            . '<ArticleCount>' . count($aryItems) . '</ArticleCount>'
            . '<Articles>';
        foreach ($aryItems as $idx => $aryValue){
            $textTpl .= '<item>'
                . '<Title><![CDATA[' . $aryValue['title'] . ']]></Title>'
                . '<Description><![CDATA[' . $aryValue['description'] . ']]></Description>'
                . '<PicUrl><![CDATA[' . $aryValue['picurl'] . ']]></PicUrl>'
                . '<Url><![CDATA[' .  $aryValue['url'] . ']]></Url>'
                .'</item>';
        }
        $textTpl .= '</Articles></xml>';
        return $textTpl;
    }


    #转发消息到指定客服
    public function transmit2Custom($object, $customerID){
        $textTpl = '<xml>'
            . '<ToUserName><![CDATA[' . $object->FromUserName . ']]></ToUserName>'
            . '<FromUserName><![CDATA[' . $object->ToUserName . ']]></FromUserName>'
            . '<CreateTime>' . time() . '</CreateTime>'
            . '<MsgType><![CDATA[transfer_customer_service]]></MsgType>'
            . '<TransInfo><KfAccount><![CDATA[' . $customerID . ']]</KfAccount></TransInfo>'
            .'</xml>';
        return $textTpl;
    }

}