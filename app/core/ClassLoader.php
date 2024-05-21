<?php


namespace app\core;

class ClassLoader
{
    protected array $aTypeDirNames = [];
    protected array $aPrefixes = [];
    public function addTypeDirName(string $sTypeDirName): self
    {
        $this->aTypeDirNames[] = $sTypeDirName;
        return $this;
    }
    public function addPrefix(string $sPrefix, string $sBaseDir): self
    {
        $sPrefix = trim($sPrefix, '\\') . '\\';
        $sBaseDir = rtrim($sBaseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->aPrefixes[] = [$sPrefix, $sBaseDir];
        return $this;
    }
    public function getTypeDirNames(): array
    {
        return $this->aTypeDirNames;
    }
    public function getPrefixes(): array
    {
        return $this->aPrefixes;
    }
    public function findFile(string $sClass): string
    {
        $sReturn = '';
        $sClass = ltrim($sClass, '\\');

        foreach ($this->aPrefixes as $aCurrent) {
            [$sCurrentPrefix, $sCurrentBaseDir] = $aCurrent;

            if (mb_strpos($sClass, $sCurrentPrefix) === 0) {
                $sClassWithoutPrefix = mb_substr($sClass, mb_strlen($sCurrentPrefix));
                $aClassWithoutPrefix = explode('\\', $sClassWithoutPrefix);
                $sModule = $aClassWithoutPrefix[0];
                if ($sModule == 'core') {
                    $sFilePath = $sCurrentBaseDir
                        . str_replace('\\', DIRECTORY_SEPARATOR, $sClassWithoutPrefix)
                        . '.php';
                    if (file_exists($sFilePath)) {
                        $sReturn = $sFilePath;
                    }
                } else {
                    $aClassWithoutModule = $aClassWithoutPrefix;
                    array_shift($aClassWithoutModule);
                    $sClassWithoutModule = implode('\\', $aClassWithoutModule);
                    foreach ($this->aTypeDirNames as $sTypeDirName) {
                        $sFilePath = $sCurrentBaseDir
                            . str_replace('\\', DIRECTORY_SEPARATOR, sprintf('%s\\%s\\%s', $sModule, $sTypeDirName, $sClassWithoutModule))
                            . '.php';
                        if (file_exists($sFilePath)) {
                            $sReturn = $sFilePath;
                            break;
                        }
                    }
                }
                break;
            }
        }

        return $sReturn;
    }
    public function loadClass(string $sClass): bool
    {
        $sFilePath = $this->findFile($sClass);
        if ($sFilePath) {
            require_once $sFilePath;
            $bReturn = true;
        } else {
            $bReturn = false;
        }
        return $bReturn;
    }
    public function register(bool $bPrepend = false)
    {
        spl_autoload_register([$this, 'loadClass'], true, $bPrepend);
    }
    public function unregister()
    {
        spl_autoload_unregister([$this, 'loadClass']);
    }
}
