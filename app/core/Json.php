<?php


namespace app\core;

final class Json
{
    public static function encode(mixed $mVar, bool $isNumCheck = false): string
    {
        if ($isNumCheck) {
            return json_encode($mVar, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode($mVar, JSON_UNESCAPED_UNICODE);
        }
    }

    public static function decode(string $sJson, bool $isAssoc = true): array
    {
        return json_decode($sJson, $isAssoc);
    }

    public static function isJson(mixed $mValue): bool
    {
        json_decode($mValue);
        return json_last_error() === JSON_ERROR_NONE;
    }

}
