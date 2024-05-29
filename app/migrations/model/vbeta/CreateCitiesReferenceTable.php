<?php

namespace app\migrations\vbeta;

use app\migrations\AbstractMigration;
use Exception;
use Illuminate\Database\Schema\Blueprint;

return new class () extends AbstractMigration
{
    public function run(): bool
    {
        try {
            if (!$this->schema->hasTable('cities_reference')) {
                $this->schema->create('cities_reference', function (Blueprint $table) {
                    $table->id();
                    $table->string('kz');
                    $table->string('ru');
                    $table->timestamps();
                });
            }
            $this->out('cities_reference table created');
            return true;
        } catch (Exception $e) {
            $this->out($e->getMessage());
            return false;
        }
    }
};