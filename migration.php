<?php
define('WWW_PATH', str_replace('\\', '/', __DIR__));
define('APP_PATH', dirname(WWW_PATH));
define('APP_MODE', 'mgrt');
set_time_limit(0);
require_once WWW_PATH . '/sys/manuscript.php';
define('PROD', config('app.site_url') === 'https://posylka.kz');
date_default_timezone_set(config('app.timezone'));

use app\core\db\DatabaseProvider;
use app\core\db\Migration;
use app\core\Util;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    DatabaseProvider::init();
    if (!Schema::hasTable('migrations')) {
        Schema::create('migrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version');
            $table->string('status');
            $table->string('output');
            $table->timestamps();
        });
    }
} catch (\Exception $exception) {
    echo $exception->getMessage();
}
Util::increaseMemoryLimit(1024);
Migration::getInstance()->execute();
