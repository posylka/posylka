<?php
define('WWW_PATH', str_replace('\\', '/', __DIR__));
define('APP_PATH', dirname(WWW_PATH));

$_PUT = $_DELETE = [];
require_once WWW_PATH . '/sys/manuscript.php';
new \app\core\Boot();
