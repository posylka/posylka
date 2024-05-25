<?php


namespace app\migrations;

use app\core\db\DB;
use Illuminate\Database\Schema\Builder as Schemabuilder;

abstract class AbstractMigration
{
    protected SchemaBuilder $schema;
    public function __construct()
    {
        $this->schema = DB::schema();
    }
    abstract function run(): bool;
    protected function out($sText): void
    {
        fwrite(fopen('php://stdout', 'w'), $sText . "\n");
        echo $sText . "\n";
    }
}
