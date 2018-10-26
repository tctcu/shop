<?php
class CommonModel extends MysqlModel {

    const IMAGE_URL = 'http://img.wzzsl.com/';//七牛地址

    const IMAGE_MIDDLE_SIZE = '?imageView2/1/w/300/h/300';
    const IMAGE_SMALL_SIZE = '?imageView2/1/w/150/h/150';

    function __construct(){
        parent::__construct();
    }
}
