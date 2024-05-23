<?php
require_once WWW_PATH . str_replace('/', DIRECTORY_SEPARATOR, '/app/core/ClassLoader.php');
use app\core\ClassLoader as ClassLoader;
$sAppPath = WWW_PATH . DIRECTORY_SEPARATOR . 'app';
$oLoader = new ClassLoader();
$oLoader->addPrefix('app', $sAppPath)
    ->addTypeDirName('controller')
    ->addTypeDirName('model')
    ->register();

require_once WWW_PATH . DIRECTORY_SEPARATOR . '/vendor/autoload.php';
