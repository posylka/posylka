<?php

//---------constants---------//
define('IS_REQUEST', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST');
define('IS_PUT', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT');
define('IS_GET', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'GET');
define('IS_DELETE', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE');
define('IS_OPTIONS', isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS');
define('SYSTEM_VERSION', file_get_contents(WWW_PATH . '/system/VERSION'));
setlocale(LC_ALL, 'en_US.UTF-8');

//---------unicode---------//
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

require_once WWW_PATH . str_replace('/', DIRECTORY_SEPARATOR, '/app/core/Singleton.php');
require_once WWW_PATH . str_replace('/', DIRECTORY_SEPARATOR, '/app/core/Config.php');

set_include_path(WWW_PATH . PATH_SEPARATOR . get_include_path());

//---------autoload---------//
require_once __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/autoload.php');

if (!function_exists('jd'))
{
    function jd($mValues)
    {
        header('Content-Type: application/json');
        echo json_encode(func_get_args(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        return \app\core\Config::getInstance()->get($key, $default);
    }
}