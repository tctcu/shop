<?php
/**
 * rsa 加解密
 * User: 冯东伟
 * Date: 17/11/16
 * Time: 下午5:22
 */

class Rsa {

    static private $privateKeyFilePath = 'rsa_private_key.pem';
    static private $publicKeyFilePath = 'rsa_public_key.pem';

    public function __construct(){
        if( !extension_loaded('openssl') )
            throw new Exception("php需要openssl扩展支持");
    }

    // 获取私钥
    static private function getPrivateKey(){
        $path = dirname(__FILE__) . '/' . self::$privateKeyFilePath;
        if( !file_exists($path) )
            return false;

        return openssl_pkey_get_private(file_get_contents($path));
    }

    // 获取公钥
    static private function getPublicKey(){
        $path = dirname(__FILE__) . '/' . self::$publicKeyFilePath;

        if( !file_exists($path) )
            return false;

        return openssl_pkey_get_public(file_get_contents($path));
    }

    // 公钥 加密
    static public function rsaPublicEncrypt($str){
        if( openssl_public_encrypt($str, $encryptData, self::getPublicKey()) )
            return $encryptData;

        return false;
    }

    // 公钥 解密
    static public function rsaPublicDecrypt($encryptStr){
        if( openssl_public_decrypt($encryptStr, $encryptData, self::getPublicKey()) )
            return $encryptData;

        return false;
    }

    // 私钥 加密
    static public function rsaPrivateEncrypt($str){
        if( openssl_private_encrypt($str, $encryptData, self::getPublicKey()) )
            return $encryptData;

        return false;
    }

    // 私钥 解密
    static public function rsaPrivateDecrypt($encryptStr){
        if( openssl_private_decrypt($encryptStr, $decryptData, self::getPrivateKey()) )
            return $decryptData;

        return false;
    }

    // 加密 转base64
    static public function encrypt($str){
        $encrypt = self::rsaPublicEncrypt($str);

        if( empty($encrypt) ) return false;

        return base64_encode($encrypt);
    }

    // 解密 base64
    static public function decrypt($str){
        $str = base64_decode($str);

        return self::rsaPrivateDecrypt($str);
    }

    // 获取 公钥
    static public function publicKey(){
        $path = dirname(__FILE__) . '/' . self::$publicKeyFilePath;

        if( !file_exists($path) )
            return '';

        $keys = explode("\n", file_get_contents($path));
        $key = '';
        for($i = 1; $i < count($keys) - 1; $i ++){
            $key .= $keys[$i];
        }

        return $key;
    }

}