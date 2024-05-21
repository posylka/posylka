<?php

namespace app\core;

trait Singleton
{
    private static $oInstance = null;
    private function __construct()
    {}
    private function __clone()
    {}
    private function __wakeup()
    {}
    protected function init()
    {}
    public static function getInstance(...$arguments): static
    {
        if(self::$oInstance === null) {
            self::$oInstance = new static();
            self::$oInstance->init(...$arguments);
        }
        return self::$oInstance;
    }

}
