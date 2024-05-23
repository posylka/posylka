<?php

namespace app\core\validation\rules;

interface RuleInterface
{
    public function validate(mixed $value, array $params = []): bool;
}
