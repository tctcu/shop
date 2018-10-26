<?php
#阿里云推送
require_once(dirname(__FILE__) . '/Aliyun/aliyun-php-sdk-core/Config.php');
include dirname(__FILE__) .'/Aliyun/aliyun-php-sdk-push/Push/Request/V20160801/PushRequest.php';
use \Push\Request\V20160801 as Push;

class AliyunPush{

    // 设置你自己的AccessKeyId/AccessSecret/AppKey
    protected $accessKeyId = "LTAIUv3lrdRv40Ie";
    protected $accessKeySecret = "vC2OnnAD8HlYCJy6weCdyABiFrgL1a";
    protected $android_appKey = "24845409";  //安卓
    protected $ios_appKey = "24844896";  //ios

    /**
     * @param $condition
     * array('type'=>自定义推送类型；'tid'=>根据推动类型取值；'title'=>推送标题，'note'=>推送内容；'platform'=>推送平台；)
     * @param string $push_token：为空表示全推,不为空表示单推
     * @return array|bool|mixed|SimpleXMLElement
     */
    public function Aliyun_SendAll($condition, $push_token = ''){
        if(empty($condition['type']) || empty($condition['tid'])){   //自定义参数
            return false;
        }

        $condition['title'] = mb_substr( $condition['title'], 0, 15, 'utf-8');  //阿里云推送的标题不能超过16个字
        $condition['note'] = mb_substr( $condition['note'], 0, 120, 'utf-8');  //阿里云推送的内容

        $ret = array();
        if($condition['platform'] == 'android'){
            $app_key = $this->android_appKey;
            $ret = $this->send($condition, $push_token,$app_key);
        }elseif ($condition['platform'] == 'ios'){
            $app_key = $this->ios_appKey;
            $ret = $this->send($condition, $push_token,$app_key);
        }else{
            #ios推送
            $app_key = $this->ios_appKey;
            $ret = $this->send($condition, $push_token,$app_key);

            #安卓推送
            $app_key = $this->android_appKey;
            $ret = $this->send($condition, $push_token,$app_key);
        }

        return $ret;
    }

    function send($condition, $push_token = '', $app_key = ''){
        $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $this->accessKeyId, $this->accessKeySecret);
        $client = new DefaultAcsClient($iClientProfile);
        $request = new Push\PushRequest();

        // 推送目标
        $request->setAppKey($app_key);
        if(empty($push_token)){  //全推
            $request->setTarget("ALL"); //推送目标: DEVICE:推送给设备; ACCOUNT:推送给指定帐号,TAG:推送给自定义标签; ALL: 推送给全部
            $request->setTargetValue("ALL"); //根据Target来设定，如Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
        }else{  //单推
            $request->setTarget("DEVICE"); //推送目标: DEVICE:推送给设备; ACCOUNT:推送给指定帐号,TAG:推送给自定义标签; ALL: 推送给全部
            $request->setTargetValue($push_token); //根据Target来设定，如Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
        }

        $request->setDeviceType("ALL"); //设备类型 ANDROID iOS ALL.
        $request->setPushType("NOTICE"); //消息类型 MESSAGE NOTICE
        $request->setTitle($condition['title']); // 消息的标题
        $request->setBody($condition['note']); // 消息的内容

        $ext_param = json_encode(array(
            "type"=>$condition['type'],
            "tid"=>$condition['tid'],
            "itemid"=>isset($condition['itemid']) ? $condition['itemid'] : ''
        ));

        // 推送配置: iOS
        $request->setiOSBadge(1); // iOS应用图标右上角角标
        $request->setiOSSilentNotification("false");//是否开启静默通知
        $request->setiOSMusic("default"); // iOS通知声音
        $request->setiOSApnsEnv("DEV");//iOS的通知是通过APNs中心来发送的，需要填写对应的环境信息。"DEV" : 表示开发环境 "PRODUCT" : 表示生产环境
        $request->setiOSRemind("false"); // 推送时设备不在线（既与移动推送的服务端的长连接通道不通），则这条推送会做为通知，通过苹果的APNs通道送达一次(发送通知时,Summary为通知的内容,Message不起作用)。注意：离线消息转通知仅适用于生产环境
        $request->setiOSRemindBody("iOSRemindBody");//iOS消息转通知时使用的iOS通知内容，仅当iOSApnsEnv=PRODUCT && iOSRemind为true时有效
        $request->setiOSExtParameters($ext_param); //自定义的kv结构,开发者扩展用 针对iOS设备

        $request->setAndroidActivity("com.alibaba.push2.demo.XiaoMiPushActivity");//设定通知打开的activity，仅当AndroidOpenType="Activity"有效
        $request->setAndroidMusic("default");//Android通知音乐
        $request->setAndroidXiaoMiActivity("com.ali.demo.MiActivity");//设置该参数后启动小米托管弹窗功能, 此处指定通知点击后跳转的Activity（托管弹窗的前提条件：1. 集成小米辅助通道；2. StoreOffline参数设为true
        $request->setAndroidXiaoMiNotifyTitle($condition['title']);
        $request->setAndroidXiaoMiNotifyBody($condition['note']);
        $request->setAndroidExtParameters($ext_param); // 设定android类型设备通知的扩展属性

        // 推送控制
        $pushTime = gmdate('Y-m-d\TH:i:s\Z', strtotime('+1 second'));//延迟3秒发送
        $request->setPushTime($pushTime);
        $expireTime = gmdate('Y-m-d\TH:i:s\Z', strtotime('+1 day'));//设置失效时间为1天
        $request->setExpireTime($expireTime);
        $request->setStoreOffline("true"); // 离线消息是否保存,若保存, 在推送时候，用户即使不在线，下一次上线则会收到

        $response = $client->getAcsResponse($request);
        return $response;
    }

}