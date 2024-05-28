<?php

namespace app\events;

use app\user\User;

class VerifyEvent implements EventInterface
{
    public function trigger(mixed $params)
    {
        /** @var User $params */

    }
}