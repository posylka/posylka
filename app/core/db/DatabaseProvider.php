<?php


namespace app\core\db;

use Illuminate\Database\Capsule\Manager as Capsule;
class DatabaseProvider
{
    protected static $instance;

    public function __construct()
    {
        $capsule = new Capsule;
        $sUrl = 'pgsql://postgres:12345678@posylka-postgres:5432/posylka';
        $capsule->addConnection([
            'driver' => config('db.driver'),
            'url' => $sUrl,
//            'host' => config('db.host'),
//            'port' => config('db.port'),
//            'database' => config('db.database'),
//            'username' => config('db.username'),
//            'password' => config('db.password'),
            'charset' => config('db.charset'),
            'collation' => config('db.collation'),
            'prefix' => config('db.prefix'),
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public static function init(): void
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
    }
}
