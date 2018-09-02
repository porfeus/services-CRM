<?php

session_start();

if( isset($_SESSION['page_user']) ){
  define('PAGE_TYPE', $_SESSION['page_user']);
}else{
  define('PAGE_TYPE', 'user');
}

$config = require '../config/config.php';
require '../components/autoload.php';

$config['controllerName'] = 'services';
$config['actionName'] = 'page';

$app = new App($config);
$app->run();
