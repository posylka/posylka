<?php

namespace app\test\private;

use app\core\RestController;
use app\core\router\Response;

class AuthTestController extends RestController
{
    public function get(): Response
    {
        return $this->response->setIsSuccess(true)
            ->setMessage('success123')
            ->setContent([]);
    }
}