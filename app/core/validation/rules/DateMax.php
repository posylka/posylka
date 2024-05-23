<?php

namespace app\core\validation\rules;

use Illuminate\Support\Carbon;

/**
 * Валдицаия максимальной даты, введенная дата входит в отрезок
 */
class DateMax implements RuleInterface
{
    public function validate(mixed $value, array $params = []): bool
    {
        $maxDate = current($params);
        $diffInDays = Carbon::parse($value)->diffInDays(Carbon::parse($maxDate), false);

        return $diffInDays >= 0;
    }
}
