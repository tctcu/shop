<?php
require(dirname(__FILE__).'/Qiniu/autoload.php');
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

class Qiniu{
	private $accessKey = 'WLyAeA6MKwNv1N_28solEkUkSwLEdvjO9Td3PBrn';
	private $secretKey = 'af8HSU7uFE_cAYrtCa24KEd9mQtxi6cFJDTce6bo';
	private $bucket = 'tctcv';

	#鉴权
	private function auth(){
		$auth = new Auth($this->accessKey, $this->secretKey);
		return $auth;
	}

	#上传
	function putFile($remote_file,$file_path,$type=''){
		//获取auth
		$auth = $this->auth();
		// 生成上传Token
		if($type=='video'){
			$remote_pic = uniqid(rand(), true) . ".jpg";
			$pfop = "vframe/jpg/offset/7/|saveas/" . \Qiniu\base64_urlSafeEncode($this->bucket .':'. $remote_pic);
			$notifyUrl = 'http://'.$_SERVER['HTTP_HOST'].'/Bd/qiniu/';//转码完成后通知到你的业务服务器。（公网可以访问，并相应200 OK）
			$pipeline = '';//独立的转码队列：https://portal.qiniu.com/mps/pipeline

			$policy = array(
				'persistentOps' => $pfop,
				'persistentNotifyUrl' => $notifyUrl,
				'persistentPipeline' => $pipeline
			);
			$token = $auth->uploadToken($this->bucket, null, 3600, $policy);
		}else{
			$token = $auth->uploadToken($this->bucket);
		}
		$uploadMgr = new UploadManager();
		$result = $uploadMgr->putFile($token,$remote_file,$file_path);
		return $result;
	}

	#管理
	function manager($type,$remote_file,$url=''){
		//获取auth
		$auth = $this->auth();
		//初始化BucketManager
		$bucketMgr = new BucketManager($auth);
		$return = false;
		switch ($type){
			case 'delete'://删除
				$result = $bucketMgr->delete($this->bucket,$remote_file);
				if(empty($result)){
					$return = true;
				}
				break;
			case 'fetch'://远程抓取上传
				if($url){
					$result = $bucketMgr->fetch($url,$this->bucket,$remote_file);
					if(isset($result[0]['key'])){
						$return = $result[0]['key'];
					}
				}
				break;
			default;
		}
		return $return;
	}

}