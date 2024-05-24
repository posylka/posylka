<?php

namespace app\exception;

use app\core\enums\HttpStatus;

class UserAlreadyExistsException extends \Exception implements HttpExceptionInterface
{
    public function getHttpCode(): HttpStatus
    {
        return HttpStatus::HTTP_RESERVED;
    }

    public function getHttpMessage(): string
    {
        return 'User with such username already exists, please try another.';
    }
}
