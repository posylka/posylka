<?php

namespace app\core\validation\rules;

class CardNumber implements RuleInterface
{
    /**
     * Валидация номера карты. Алгоритм Луна
     */
    public function validate(mixed $value, array $params = []): bool
    {
        $cardNumber = preg_replace('/\D/', '', $value);
        $digits = str_split($cardNumber);
        $digits = array_reverse($digits);

        for ($i = 1; $i < count($digits); $i += 2) {
            $digits[$i] *= 2;
            if ($digits[$i] > 9) {
                $digits[$i] -= 9;
            }
        }
        $sum = array_sum($digits);

        return ($sum % 10 === 0);
    }
}
