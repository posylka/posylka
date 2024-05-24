<?php

namespace app\exception;

use app\core\enums\HttpStatus;

interface HttpExceptionInterface
{
    public function getHttpCode(): HttpStatus;

    public function getHttpMessage(): string;
}