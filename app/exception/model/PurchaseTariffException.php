<?php

namespace app\exception;

use app\core\enums\HttpStatus;

class PurchaseTariffException extends \Exception implements HttpExceptionInterface
{
    public function getHttpCode(): HttpStatus
    {
        return HttpStatus::HTTP_NOT_ACCEPTABLE;
    }

    public function getHttpMessage(): string
    {
        return 'Your current tariff plan is a bonus and can not be renewed, please purchase tariff plan.';
    }
}
