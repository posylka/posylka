<?php


namespace app\core\validation;

use app\core\validation\rules\CardNumber;
use app\core\validation\rules\DateMax;
use app\core\validation\rules\DateMin;
use app\core\validation\rules\IsInteger;
use app\core\validation\rules\KzPhoneNumber;
use app\core\validation\rules\Required;

class Rule
{
    /**
     * @var array<string, class-string>
     */
    public static array $rules = [
        'required'        => Required::class,
        'int'             => IsInteger::class,
        'integer'         => IsInteger::class,
        'card-number'     => CardNumber::class,
        'kz-phone-number' => KzPhoneNumber::class,
        'date-max'        => DateMax::class,
        'date-min'        => DateMin::class,
    ];
}