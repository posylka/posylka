<?php


namespace app\migrations;

use app\core\enums\MigrationStatus;
use Illuminate\Database\Eloquent\Model;

class Migrations extends Model
{
    /**
     * Значения по умолчанию для атрибутов модели.
     *
     * @var array
     */
    protected $attributes = [
        'version' => SYSTEM_VERSION,
        'status' => MigrationStatus::WAITING,
    ];

}
