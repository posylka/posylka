<?php

namespace app\reference;

interface ReferenceInterface
{
    public static function getValueById(int $id): mixed;

    public static function getIdByValue(string $value): int;


}
