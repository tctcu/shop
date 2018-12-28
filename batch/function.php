<?php

function post_curl($url,$data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result_json = curl_exec($ch);
    curl_close($ch);
    return  json_decode($result_json, true);
}

function post_json_curl($url,$data){
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

function get_curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result_json = curl_exec($ch);
    curl_close($ch);
    return  json_decode($result_json, true);
}


function hdk_log($word='') {
    $fp = fopen("/home/wwwlogs/hdk.txt","a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"\n".$word."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}