<?php
require_once(dirname(__FILE__).'/CloudFileSystem/uploader.php');

class CloudFileSystem{
	private $size = 20971520; #20M
	private $type = 'jpeg,jpg,gif,png,JPG,GIF,PNG,mp4,mov,3gp,apk';

	#文件上传至七牛
	function upload_file($upload_field = '',$remote_file = ''){
		$uploader = new Helper_Uploader();
		if($uploader->existsFile($upload_field)){
			$host_file = $uploader->file($upload_field);
			if(!$host_file->isValid($this->type, $this->size)){
				return false;
			}
			$qiniu = new Qiniu();
			$qiniu->putFile($remote_file,$host_file->tmpFilename());
			return $remote_file;
		}
		return false;
	}

	#视频上传至七牛
	function upload_video($upload_field = '',$remote_file = ''){
		$uploader = new Helper_Uploader();
		if($uploader->existsFile($upload_field)){
			$host_file = $uploader->file($upload_field);
			if(!$host_file->isValid($this->type, $this->size)){
				return false;
			}
			$qiniu = new Qiniu();
			$result = $qiniu->putFile($remote_file,$host_file->tmpFilename(),'video');
			$return = array();
			if(isset($result[0]['key'])){
				$return['link'] = $result[0]['key'];
				$return['persistentId'] = $result[0]['persistentId'];//进程ID用于回调更新封面图
			}
			return $return;
		}
		return false;
	}

	#base64上传至七牛
	function upload_base64($base64_string = '',$remote_file = ''){
		$tmp_remote_file = str_replace('/', '.', $remote_file);//防止创建2级目录
		$tmpFilename = '/tmp/'.$tmp_remote_file;
		$ifp = fopen($tmpFilename,"wb");
		$string = @base64_decode(str_pad(strtr($base64_string, '-_', '+/'), strlen($base64_string) % 4, '=', STR_PAD_RIGHT));
		fwrite($ifp,$string);
		fclose($ifp);
		$qiniu = new Qiniu();
		$qiniu->putFile($remote_file,$tmpFilename);
		unlink($tmpFilename);
		return $remote_file;
	}

	#远程url上传至七牛
	function upload_fetch($url,$remote_file=''){
		if(empty($remote_file)){
			$ext_name = substr(strrchr($url,'.'),1);
			$remote_file = uniqid(rand(), true) . "." . $ext_name;
		}

		if($url && $remote_file) {
			$qiniu = new Qiniu();
			$result = $qiniu->manager('fetch',$remote_file, $url);
			return $result;
		}
		return false;
	}

	#删除指定七牛文件
	function delete($remote_file){
		if($remote_file) {
			$qiniu = new Qiniu();
			$result = $qiniu->manager('delete',$remote_file);
			return $result;
		}
		return false;
	}



	#上传到本地
	function uploadLocal($upload_field = '', $newanme = '', $rotate_flag = 0, $gen_clear = 0, $newwidth = 400, $newheight=400){

		$newpath = APPLICATION_PATH.$newanme;
		$this->mkdirs(dirname($newpath));
		$tmp_name = $_FILES[$upload_field]['tmp_name'];
		if($rotate_flag) {
			$is_rotate = $this->checkRotate($tmp_name);
		} else {
			$is_rotate = 0;
		}

		$attach_saved = false;
		if(@copy($tmp_name, $newpath) || (function_exists('move_uploaded_file') && @move_uploaded_file($tmp_name, $newpath)) || @rename($tmp_name, $newpath)) {
			@unlink($tmp_name);
			$attach_saved = true;
		}
		if(!$attach_saved && is_readable($tmp_name)) {
			$fp = @fopen($tmp_name, 'rb');
			@flock($fp, 2);
			$attachedfile = @fread($fp, $_FILES[$upload_field]['size']);
			@fclose($fp);

			$fp = @fopen($newpath, 'wb');
			@flock($fp, 2);
			if(@fwrite($fp, $attachedfile)) {
				@unlink($tmp_name);
				$attach_saved = true;
			}
			@fclose($fp);
		}
		if(!$attach_saved) {
			return false;
		} else {
			if($gen_clear) {
				$this->genClearImage($newpath, $newpath, $newwidth, $newheight, $is_rotate);
			}
			return true;
		}
	}
	function delete_local($remote_file){
		@unlink(APPLICATION_PATH.$remote_file);
		return ;
	}

	function mkdirs($dir) {
		if(!is_dir($dir)) {
			if(!$this->mkdirs(dirname($dir))) {
				return false;
			}
			if(!mkdir($dir, 0777)){
				return false;
			}
		}
		return true;
	}
	function checkRotate($image_file) {
		$exif = exif_read_data($image_file);//获取exif信息
		if (isset($exif['Orientation']) && $exif['Orientation'] == 6) {
			//旋转
			return 1;
		} else {
			return 0;
		}
	}
	#处理图片精度和大小
	function genClearImage($newpath, $outpath, $newWidth=400, $newHeight=400, $is_rotate=0) {
		$originUrl = $newpath;
		$destUrl = $outpath;
		if (false !== $pos = strrpos($originUrl, ".")) {
			$originType = substr($originUrl, $pos + 1);
		}
		if ("gif" == strtolower ($originType)) {
			$originImg = imagecreatefromgif($originUrl);
		} else if ("jpg" == strtolower ($originType) || "jpeg" == strtolower ($originType)) {
			$originImg = imagecreatefromjpeg($originUrl);
		} else if ("png" == strtolower ($originType)) {
			$originImg = imagecreatefrompng($originUrl);
		} else {
			return true;
		}
		if($is_rotate == 1) {
			$originImg = imagerotate($originImg, -90, 0);
		}
		$originX = imagesx($originImg);
		$originY = imagesy($originImg);
		if (0 == $newWidth || 0 > $newWidth || $originX < $newWidth) {
			$newWidth = $originX;
		}
		if (0 == $newHeight || 0 > $newHeight || $originY < $newHeight) {
			$newHeight = $originY;
		}
		$scaleX = $originX / $newWidth;
		$scaleY = $originY / $newHeight;
		$scale = $scaleX > $scaleY ? $scaleX : $scaleY;
		$destImg = imagecreatetruecolor($originX / $scale, $originY / $scale);
		imagecopyresampled($destImg, $originImg, 0, 0, 0, 0, $originX / $scale, $originY / $scale, $originX, $originY);
		if (false !== $pos = strrpos($destUrl, ".")) {
			$destType = substr($destUrl, $pos + 1);
		}
		if ("gif" == strtolower ($destType)) {
			imagegif($destImg, $destUrl);
		} else if ("jpg" == strtolower ($destType) || "jpeg" == strtolower ($destType)) {
			imagejpeg($destImg, $destUrl);
		} else if ("png" == strtolower ($destType)) {
			imagepng($destImg, $destUrl);
		} else {
			return false;
		}
		return true;
	}


}