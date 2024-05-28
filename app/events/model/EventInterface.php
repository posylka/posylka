<?php

namespace app\events;

interface EventInterface
{
    public function trigger(mixed $params);
}
