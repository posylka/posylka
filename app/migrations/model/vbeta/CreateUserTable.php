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
            if (!$this->schema->hasTable('user')) {
                $this->schema->create('user', function (Blueprint $table) {
                    $table->id();
                    $table->string('username')->unique();
                    $table->string('password');
                    $table->string('phone');
                    $table->string('status');
                    $table->timestamps();
                });
            }
            $this->out('user table created');
            return true;
        } catch (Exception $e) {
            $this->out($e->getMessage());
            return false;
        }
    }
};