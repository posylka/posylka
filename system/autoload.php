<?php
require_once WWW_PATH . str_replace('/', DIRECTORY_SEPARATOR, '/app/core/ClassLoader.php');
use app\core\ClassLoader as ClassLoader;
$sAppPath = WWW_PATH . DIRECTORY_SEPARATOR . 'app';
$oLoader = new ClassLoader();
$oLoader->addPrefix('app', $sAppPath)
    ->addTypeDirName('action')
    ->addTypeDirName('model')
    ->register();

// for composer require_once WWW_PATH . DIRECTORY_SEPARATOR . 'lib' . '/vendor/autoload.php';
