<?php


namespace app\core\db;

use Illuminate\Database\Capsule\Manager as Capsule;
class DatabaseProvider
{
    protected static $instance;

    public function __construct()
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => config('db.driver'),
            'host' => config('db.host'),
            'database' => config('db.database'),
            'username' => config('db.username'),
            'password' => config('db.password'),
            'charset' => config('db.charset'),
            'collation' => config('db.collation'),
            'prefix' => config('db.prefix'),
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public static function init(string $schema = 'operational'): void
    {
        if (!isset(self::$instance)) {
            self::$instance = new static($schema);
        }
    }
}
