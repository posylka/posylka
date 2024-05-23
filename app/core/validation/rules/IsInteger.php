<?php

namespace app\core\validation\rules;

/**
 * Проверка на целое число
 */
class IsInteger implements RuleInterface
{
    public function validate(mixed $value, array $params = []): bool
    {
        return is_int($value);
    }
}
