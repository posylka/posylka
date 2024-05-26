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
            if (!$this->schema->hasTable('tokens')) {
                $this->schema->create('tokens', function (Blueprint $table) {
                    $table->id();
                    $table->string('user_id');
                    $table->string('user_agent');
                    $table->text('token');
                    $table->timestamps();
                });
            }
            $this->out('tokens table created');
            return true;
        } catch (Exception $e) {
            $this->out($e->getMessage());
            return false;
        }
    }
};