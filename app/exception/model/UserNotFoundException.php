<?php

namespace app\exception;

use app\core\enums\HttpStatus;

class UserNotFoundException extends \Exception implements HttpExceptionInterface
{
    public function getHttpCode(): HttpStatus
    {
        return HttpStatus::HTTP_NOT_FOUND;
    }

    public function getHttpMessage(): string
    {
        return 'User with such username not found.';
    }
}
