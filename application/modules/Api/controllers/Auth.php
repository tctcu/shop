<?php
#配置
class AuthController extends ApiController
{

    function init()
    {
        parent::init();
    }

    function configAction(){
        $data = array(
            'category' => TbModel::CATEGORY,
            'tab' => [
                '首页',
                '分类',
                '我的'
            ],
            'wechat' => [
                'url' => 'http://img.wzzsl.com/11121647195bd3427ea1d840.19166580.jpg',
                'name' => 'quangoushengqian'
            ]
        );

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }


    #banner 广告位
    function bannerAction(){
        $banner_model = new BannerModel();
        $page =1;
        $page_size =20;
        $condition = [
            'position' => 'banner',
        ];
        $show_list = $banner_model->getListData($page,$page_size,$condition);
        $banner = [];
        foreach($show_list as $val){
            $banner[] = [
                'pic' => CommonModel::IMAGE_URL . $val['pic'],
                'type' => $val['type'],
                'goto' => $val['goto'],
            ];
        }
        $data = [
            'banner' => $banner,
        ];
        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #安卓更新
    function androidUpdateAction(){

        //小米渠道，审核中：返回空给客户端（因为审核中时不能有提示）

        $data = [
            "UpdateStatus" => '1',// 1:普通下载 2: 强制更新
            "VersionCode" => '1',  // VersionCode
            "VersionName" => "1.0.0", // VersionName
            "UploadTime" => "2019-01-04 17:28:41", // 更新时间
            "ModifyContent" => "这是一个很有意义的更新", // 更新描述
            "DownloadUrl" => CommonModel::IMAGE_URL.'package_5271_1517565084.apk', // 下载链接
            "ApkSize" => '204823',  // 应用大小
            "ApkMd5" => "bsud274be05y19365do0562he"   // apkMd5值
        ];

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

    #苹果更新
    function iosUpdateAction(){

        $data = [
            "UpdateStatus" => '1',// 1:普通下载 2: 强制更新
            "VersionName" => "1.1.3", // VersionName
            "UploadTime" => "2019-04-09 17:28:41", // 更新时间
            "ModifyContent" => "这是一个很有意义的更新", // 更新描述
            "DownloadUrl" => 'https://itunes.apple.com/cn/app/id1440226599?mt=8', // 下载链接
        ];

        $this->responseJson(self::SUCCESS_CODE, self::SUCCESS_MSG, $data);
    }

}