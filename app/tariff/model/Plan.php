<?php

namespace app\tariff;

class Plan
{
    public static function get(string $identifier): PlanObject
    {
        return match ($identifier) {
            'verify_bonus' => new PlanObject(
                'verify_bonus',
                'Bonus for verification',
                0,
                strtotime('+170 days'),
                false
            ),
            default => new PlanObject(
                'standard',
                'Standard plan for 7 days',
                500,
                strtotime('+7 days')
            ),
        };
    }
}