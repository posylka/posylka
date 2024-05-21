<?php


namespace app\core\router;

class RoutingHelper
{
    use \app\core\Singleton;

    protected string $sBaseDir = '';
    protected string $sPrefixChar = '';
    protected array $aControllerDirNames = [];
    protected array $aExcludeDirNames = [];
    protected array $aModuleNames = [];
    public function getBaseDir(): string
    {
        return $this->sBaseDir;
    }

    public function getPrefixChar(): string
    {
        return $this->sPrefixChar;
    }

    public function getExcludeDirNames(): array
    {
        return $this->aExcludeDirNames;
    }

    public function getControllerDirNames(): array
    {
        return $this->aControllerDirNames;
    }

    public function getModuleNames(): array
    {
        return $this->aModuleNames;
    }

    public function setBaseDir(string $sBaseDir): self
    {
        $this->sBaseDir = $sBaseDir;

        return $this;
    }

    public function setPrefixChar(string $sPrefixChar): self
    {
        $this->sPrefixChar = $sPrefixChar;

        return $this;
    }

    public function addExcludeDirName(string $sExcludeDirName): self
    {
        $this->aExcludeDirNames[] = $sExcludeDirName;

        return $this;
    }

    public function addControllerDirName(string $sControllerDirName): self
    {
        $this->aControllerDirNames[] = $sControllerDirName;

        return $this;
    }

    public function addModuleName(string $sModuleName): self
    {
        $this->aModuleNames[] = $sModuleName;

        return $this;
    }


}
