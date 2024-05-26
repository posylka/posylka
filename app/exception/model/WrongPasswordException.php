<?php

namespace app\exception;

use app\core\enums\HttpStatus;

class WrongPasswordException extends \Exception implements HttpExceptionInterface
{
    public function getHttpCode(): HttpStatus
    {
        return HttpStatus::HTTP_UNAUTHORIZED;
    }

    public function getHttpMessage(): string
    {
        return 'Wrong password.';
    }
}
