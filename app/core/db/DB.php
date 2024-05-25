<?php

namespace app\core\db;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder as Schemabuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DB
{
    protected static Connection $connection;

    public static function table($table, $as = null, $connection = null): QueryBuilder
    {
        return Manager::table($table, $as, $connection);
    }

    public static function getConnection(): Connection
    {
        if (!isset(self::$connection)) {
            self::$connection = Manager::connection();
        }

        return self::$connection;
    }

    public static function schema(): Schemabuilder
    {
        return Manager::schema();
    }

}