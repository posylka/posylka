<?php

namespace app\test\any;

use app\core\RestController;
use app\core\router\Response;

class TestController extends RestController
{
    public function get(): Response
    {
        return $this->response->setIsSuccess(true)
            ->setMessage('success123')
            ->setContent([]);
    }
}