<?php

namespace app\core\validation\rules;

use Illuminate\Support\Carbon;

/**
 * Валдицаия минимальной даты, введенная дата входит в отрезок
 */
class DateMin implements RuleInterface
{
    public function validate(mixed $value, array $params = []): bool
    {
        $minDate = current($params);
        $diffInDays = Carbon::parse($minDate)->diffInDays(Carbon::parse($value), false);

        return $diffInDays > 0;
    }
}
