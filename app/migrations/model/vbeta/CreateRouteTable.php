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
            if (!$this->schema->hasTable('routes')) {
                $this->schema->create('routes', function (Blueprint $table) {
                    $table->id();
                    $table->string('city_from')->nullable(false);
                    $table->string('city_to')->nullable(false);
                    $table->timestamp('datetime')->nullable(false);
                    $table->string('description');
                    $table->string('renew')->nullable(false);
                    $table->string('user_id')->nullable(false);
                    $table->json('user_data')->nullable(false);
                    $table->timestamps();
                });
            }
            $this->out('routes table created');
            return true;
        } catch (Exception $e) {
            $this->out($e->getMessage());
            return false;
        }
    }
};