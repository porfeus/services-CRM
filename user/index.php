<?php
//error_reporting(E_ALL);

define('PAGE_TYPE', 'user');

$config = require '../config/config.php';
require '../components/autoload.php';

$config['urlManager'] = [
  'userpanel' => 'main/login',
  'renewal' => 'main/renewal',
];

$app = new App($config);
$app->run();
