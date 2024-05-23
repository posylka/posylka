<?php

namespace app\core\validation\rules;

/**
 * Валидация КЗ тел номеров
 */
class KzPhoneNumber implements RuleInterface
{
    public function validate(mixed $value, array $params = []): bool
    {
        return preg_match('/^[78]7(\d{2})(\d{7})$/', preg_replace('/[^0-9]/', '', $value)) === 1;
    }
}
