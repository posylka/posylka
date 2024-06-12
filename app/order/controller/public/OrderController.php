<?php

namespace app\order\public;

use app\core\RestController;
use app\core\router\Response;
use app\order\Order;

class OrderController extends RestController
{
    public function get(): Response
    {
        return $this->response->setContent(Order::query()->findOrFail($this->getParam(0))->toArray());
    }
}