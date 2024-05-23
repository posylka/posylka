<?php

namespace app\core\validation\rules;

/**
 * Проверка на обязательность
 */
class Required implements RuleInterface
{
    public function validate(mixed $value, array $params = []): bool
    {
        if (is_array($value)) {
            return count($value) > 0;
        }

        if (is_bool($value)) {
            return $value;
        }

        return $value !== '' && $value !== null;
    }
}
