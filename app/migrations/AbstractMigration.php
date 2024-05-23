<?php


namespace app\migrations;

abstract class AbstractMigration
{
    abstract function run(): bool;
    protected function out($sText)
    {
        fwrite(fopen('php://stdout', 'w'), $sText . "\n");
        echo $sText . "\n";
    }
}
