<?php
define("APPLICATION_PATH",  realpath(dirname(__FILE__) . '/../'));
define("APPLICATION_COINFIG_FILE",APPLICATION_PATH . "/conf/application.ini");
date_default_timezone_set('Asia/Chongqing');

$app  = new Yaf_Application(APPLICATION_COINFIG_FILE);
$app->bootstrap()->run();
