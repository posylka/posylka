<?php


namespace app\core\router;

use app\core\Util;

class Request
{
    protected RoutingHelper|null $oHelper = null;
    protected string $sPath = '';
    protected string $sModuleName = '';
    protected string $sSubmoduleName = '';
    protected string $sControllerName = '';
    protected string $sControllerNameDelimiter = '';
    protected array $aParams = [];
    protected static Request $instance;
    protected array $get = [];
    protected array $post = [];
    protected array $server = [];

    //todo put, delete

    public function __construct()
    {
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->server = $_SERVER ?? [];
    }

    public static function getInstance(): Request
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function method()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function hasGet(string $key): bool
    {
        return !is_null($this->get($key));
    }

    public function hasPost(string $key): bool
    {
        return !is_null($this->post($key));
    }

    public function getHeader(string $header)
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $header));

        return $this->server[$serverKey] ?? '';
    }

    public function getPath(): string
    {
        return $this->sPath;
    }

    public function getModuleName(): string
    {
        return $this->sModuleName;
    }

    public function getSubmoduleName(): string
    {
        return $this->sSubmoduleName;
    }

    public function getControllerName(): string
    {
        return $this->sControllerName;
    }

    public function getControllerNameDelimiter(): string
    {
        return $this->sControllerNameDelimiter;
    }

    public function getParams(): array
    {
        return $this->aParams;
    }

    final public function getParam(int $id, $default = '')
    {
        return Util::getParam($this->aParams, $id, $default);
    }

    public function setHelper(RoutingHelper $oHelper): self
    {
        $this->oHelper = $oHelper;

        return $this;
    }

    public function setPath(string $sPath): self
    {
        $this->sPath = mb_strtolower($sPath);

        return $this;
    }

    public function setModuleName(string $sModuleName): self
    {
        $this->sModuleName = $sModuleName;

        return $this;
    }

    public function setSubmoduleName(string $sSubmoduleName): self
    {
        $this->sSubmoduleName = $sSubmoduleName;

        return $this;
    }

    public function setControllerName(string $sControllerName): self
    {
        $this->sControllerName = $sControllerName;

        return $this;
    }

    public function setControllerNameDelimiter(string $sControllerNameDelimiter): self
    {
        $this->sControllerNameDelimiter = $sControllerNameDelimiter;

        return $this;
    }

    public function setParams(array $aParams): self
    {
        $this->aParams = $aParams;

        return $this;
    }

    public function process(): void
    {
        assert($this->oHelper instanceof RoutingHelper);
        $aPath = explode('/', $this->sPath);
        $aPathParts = [];
        foreach ($aPath as $sPathPart) {
            if (mb_strlen($sPathPart)) {
                $aPathParts[] = $sPathPart;
            }
        }

        if (count($aPathParts)) {
            $this->setModuleName($aPathParts[0]);
            array_shift($aPathParts);
        }

        if (count($aPathParts)) {
            foreach ($this->oHelper->getControllerDirNames() as $sControllerDirName) {
                $aDirs = glob(
                    str_replace('/', DIRECTORY_SEPARATOR, vsprintf('%s/%s/%s/*', [
                        $this->oHelper->getBaseDir(), $this->sModuleName, $sControllerDirName
                    ])),
                    GLOB_ONLYDIR
                );
                $aSubmoduleNames = [];
                foreach ($aDirs as $sV) {
                    $aDir = explode(DIRECTORY_SEPARATOR, $sV);
                    $sSubmoduleName = end($aDir);
                    if (!in_array($sSubmoduleName, $this->oHelper->getExcludeDirNames())) {
                        $aSubmoduleNames[] = $sSubmoduleName;
                    }
                }
                if (in_array($aPathParts[0], $aSubmoduleNames)) {
                    $this->setSubmoduleName($aPathParts[0]);
                    array_shift($aPathParts);
                    break;
                }
            }
        }

        if (count($aPathParts)) {
            $this->setControllerName($aPathParts[0]);
            array_shift($aPathParts);
        }

        if (count($aPathParts)) {
            $this->setParams($aPathParts);
            array_shift($aPathParts);
        }
    }

}
