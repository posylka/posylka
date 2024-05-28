<?php

namespace app\events;

use app\tariff\Plan;
use app\tariff\Tariff;
use app\user\User;

class VerifyEvent implements EventInterface
{
    public function trigger(mixed $params)
    {
        /** @var User $params */
        $plan = Plan::get('verify_bonus');
        try {
            Tariff::purchase($params, $plan);
        } catch (\Exception $exception) {
            // todo Logger log
        }

    }
}