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
            if (!$this->schema->hasTable('verify')) {
                $this->schema->create('verify', function (Blueprint $table) {
                    $table->id();
                    $table->string('user_id')->unique();
                    $table->string('code');
                    $table->string('classname');
                    $table->string('instance_id');
                    $table->json('data');
                    $table->timestamps();
                });
            }
            $this->out('verify table created');
            return true;
        } catch (Exception $e) {
            $this->out($e->getMessage());
            return false;
        }
    }
};