<?php

namespace app\reference;

interface ReferenceInterface
{
    public static function getValueById(int $id): string;

    public static function getIdByValue(string $value): int;


}
