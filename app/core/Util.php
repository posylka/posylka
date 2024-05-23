<?php


namespace app\core;


final class Util
{
    public static function getParam(array $aSource, string $sParamName, mixed $mDefaultValue = null): mixed
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

    public static function clearTmp(?string $path = null): void
    {
        if (empty($path)) {
            $path = config('app.tmp_path');
        }
        $files = glob($path . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $itemPath) {
            if (!is_dir($itemPath)) {
                unlink($itemPath);
            } else {
                self::clearTmp($itemPath);
                rmdir($itemPath);
            }
        }
    }

    public static function increaseMemoryLimit(int $iNewMemory): void
    {
        $sCurrentMemoryLimit = ini_get('memory_limit');
        if (stristr($sCurrentMemoryLimit, 'G')) {
            $iCurrentMemoryLimit = (int)str_replace('G', '', $sCurrentMemoryLimit) * 1024;
        } elseif (stristr($sCurrentMemoryLimit, 'K')) {
            $iCurrentMemoryLimit = (int)str_replace('K', '', $sCurrentMemoryLimit) / 1024;
        } elseif (stristr($sCurrentMemoryLimit, 'M')) {
            $iCurrentMemoryLimit = (int)str_replace('M', '', $sCurrentMemoryLimit);
        } else {
            $iCurrentMemoryLimit = (int)$sCurrentMemoryLimit / (1024 * 1024);
        }

        if ($iNewMemory > $iCurrentMemoryLimit) {
            ini_set('memory_limit', intval($iNewMemory) . 'M');
        }
    }
}