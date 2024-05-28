<?php

namespace app\exception;

use app\core\enums\HttpStatus;

class NotEnoughBalanceException extends \Exception implements HttpExceptionInterface
{
    public function getHttpCode(): HttpStatus
    {
        return HttpStatus::HTTP_NOT_ACCEPTABLE;
    }

    public function getHttpMessage(): string
    {
        return 'Your balance is not enough to purchase tariff plan.';
    }
}
