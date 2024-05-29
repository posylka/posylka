<?php

namespace app\reference;

class Reference
{
    public static function get(string $name): ReferenceInterface
    {
        return match ($name) {
            'cities' => new CitiesReference(),
            default => new DefaultReference(),
        };
    }
}