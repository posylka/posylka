<?php

namespace app\something\public;

use app\core\db\Migration;
use app\core\RestController;
use app\core\router\Response;

class MigrateController extends RestController
{
    public function get(): Response
    {
        define('APP_MODE', 'mgrt');
        $m = Migration::getInstance();
        $m->execute();
        return $this->response;
    }

    function hasAccess(): bool
    {
        return !PROD && $this->get('key') === config('app.mgrt-key');
    }
}
