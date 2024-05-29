<?php

namespace app\reference;

class DefaultReference implements ReferenceInterface
{

    public static function getValueById(int $id): string
    {
        return '';
    }

    public static function getIdByValue(string $value): int
    {
        return 0;
    }
}
