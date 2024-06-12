<?php

namespace app\route\public;

use app\core\RestController;
use app\core\router\Response;
use app\route\Route;

class RouteController extends RestController
{
    public function get(): Response
    {
        return $this->response->setContent(Route::query()->findOrFail($this->getParam(0))->toArray());
    }
}