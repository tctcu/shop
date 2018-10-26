<?php
class WebController extends Yaf_Controller_Abstract
{
    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    #协议
    function protocolAction(){}

    #官网
    function pcAction(){
        echo 'test';
    }


}
