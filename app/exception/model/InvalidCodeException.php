<?php

namespace app\exception;

use app\core\enums\HttpStatus;

class InvalidCodeException extends \Exception implements HttpExceptionInterface
{
    public function getHttpCode(): HttpStatus
    {
        return HttpStatus::HTTP_NOT_ACCEPTABLE;
    }

    public function getHttpMessage(): string
    {
        return 'Wrong security code.';
    }
}
