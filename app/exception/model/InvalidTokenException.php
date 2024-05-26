<?php

namespace app\exception;

use app\core\enums\HttpStatus;

class InvalidTokenException extends \Exception implements HttpExceptionInterface
{
    public function getHttpCode(): HttpStatus
    {
        return HttpStatus::HTTP_UNAUTHORIZED;
    }

    public function getHttpMessage(): string
    {
        return 'Invalid token.';
    }
}
