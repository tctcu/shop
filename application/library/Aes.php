<?php
/**
 * aes AES/CBC/PKCS5Padding
 * User: apple
 * Date: 17/11/20
 * Time: 下午1:03
 */

class Aes {

    public function __construct(){
        if( !extension_loaded('mcrypt') )
            throw new Exception("php需要mcrypt扩展支持");
    }

    // 密钥
    static private function secretKey($key, $bit = 128){
        if( $bit == 256 )
            return hash('SHA256', $key, true);

        return hash('MD5', $key, true);
    }

    // 偏移量
    static private function iv($iv = ""){
        if( $iv != "" )
            return hash('MD5', $iv, true);

        return chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0); //IV is not set. It doesn't recommend.
    }

    // aes 加密
    static private function aesEncrypt($str, $key, $bit = 128, $iv = '') {
        $key = self::secretKey($key, $bit);
        $iv = self::iv($iv);

        //Open
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $key, $iv);

        //Padding
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC); //Get Block Size
        $pad = $block - (strlen($str) % $block); //Compute how many characters need to pad
        $str .= str_repeat(chr($pad), $pad); // After pad, the str length must be equal to block or its integer multiples

        //Encrypt
        $encrypted = mcrypt_generic($module, $str);

        //Close
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        return $encrypted;
    }

    // aes 解密
    static private function aesDecrypt($str, $key, $bit = 128, $iv = '') {
        $key = self::secretKey($key, $bit);
        $iv = self::iv($iv);

        //Open
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $key, $iv);

        //Decrypt
        $str = mdecrypt_generic($module, $str); //Get original str

        //Close
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        //Depadding
        $slast = ord(substr($str, -1)); //pad value and pad count
        $str = substr($str, 0, strlen($str) - $slast);

        //Return
        return $str;
    }

    /**
     * aes 加密 生成base64
     * @param string $str 要加密的字符串
     * @param string $key 加密的key
     * @param int $bit 加密位数 128
     * @param string $iv 加密偏移量
     * @return string
    */
    static public function encrypt($str, $key, $bit = 128, $iv = '0123456789ABCDEF'){
        return base64_encode(self::aesEncrypt($str, $key, $bit, $iv));
    }

    /**
     * aes 加密否的base64字符串解密
     * @param string $str 要解密的字符串
     * @param string $key 解密的key
     * @param int $bit 解密位数 128
     * @param string $iv 解密偏移量
     * @return string
     */
    static public function decrypt($str, $key, $bit = 128, $iv = '0123456789ABCDEF'){
        return self::aesDecrypt(base64_decode($str), $key, $bit, $iv);
    }
}