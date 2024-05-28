<?php

namespace app\events;

class Event
{
    public static function get(string $name): EventInterface
    {
        return match ($name) {
            'signUp' => new SignUpEvent(),
            'verify' => new VerifyEvent(),
            default => new DefaultEvent(),
        };
    }
}