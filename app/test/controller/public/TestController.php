<?php

namespace app\test\public;

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