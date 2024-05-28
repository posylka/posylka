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
            if (!$this->schema->hasTable('tariff')) {
                $this->schema->create('tariff', function (Blueprint $table) {
                    $table->id();
                    $table->string('user_id')->unique();
                    $table->string('identifier');
                    $table->string('name');
                    $table->bigInteger('start_time');
                    $table->integer('balance');
                    $table->timestamps();
                });
            }
            $this->out('tariff table created');
            return true;
        } catch (Exception $e) {
            $this->out($e->getMessage());
            return false;
        }
    }
};