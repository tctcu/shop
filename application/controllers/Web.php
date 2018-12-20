<?php
class WebController extends Yaf_Controller_Abstract
{
    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    #协议
    function protocolAction(){}
    #引导识别淘口令
    function courseAction(){}

    #拼多多回调
    function pddAction(){
        echo  'success';die;
    }


}
