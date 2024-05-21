<?php
define('WWW_PATH', str_replace('\\', '/', __DIR__));
define('APP_PATH', dirname(WWW_PATH));

$aConfig = $_PUT = $_DELETE = [];
require_once WWW_PATH . '/sys/manuscript.php';

define('PROD', $aConfig['app_frontend_site_url'] === 'https://posylka.kz');
new \app\core\Boot($aConfig);
