<?php


namespace app\core;


final class Util
{
    public static function getParam($aSource, $sParamName, $mDefaultValue = null)
    {
        if (isset($aSource[$sParamName])) {
            if (is_string($aSource [$sParamName])) {
                return trim($aSource[$sParamName]);
            } else {
                return $aSource[$sParamName];
            }
        } else {
            return $mDefaultValue;
        }
    }
}