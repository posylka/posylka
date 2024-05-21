<?php


namespace app\core\router;

use app\core\RestController;
use app\core\router\Request;

class Dispatcher
{
    protected array $aFilePaths = [];
    protected Request|null $oRequest = null;
    protected array $aPrefix = [];
    protected array $aControllerDirNames = [];
    public function getRequest(): Request
    {
        return $this->oRequest;
    }

    public function getPrefix(): array
    {
        return $this->aPrefix;
    }

    public function getControllerDirNames(): array
    {
        return $this->aControllerDirNames;
    }

    protected function setRequest(Request $oRequest): self
    {
        $this->oRequest = $oRequest;

        return $this;
    }

    public function setPrefix(string $sPrefix, string $sBaseDir): self
    {
        $sPrefix = trim($sPrefix, '\\') . '\\';
        $sBaseDir = rtrim($sBaseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->aPrefix = [$sPrefix, $sBaseDir];

        return $this;
    }

    public function addControllerDirName(string $sControllerDirName): self
    {
        $this->aControllerDirNames[] = $sControllerDirName;

        return $this;
    }

    public function handle(Request $oRequest): RestController
    {
        $this->setRequest($oRequest);
        $aResponse = $this->process();
        if ($aResponse['is_set']) {
            $oController = new $aResponse['controller']($aResponse['params']);
            if ($aResponse['access'] === 'user' && $oController instanceof RestController) {
                $oController->setNeedSession(true);
            }
        } else {
            header("404 Not Found");
            print '404';
            exit;
        }
        return $oController;
    }

    public function process(): array
    {
        [$sPrefix, $sBaseDir] = $this->aPrefix;
        $sSubmoduleName = '';
        if (mb_strlen($this->oRequest->getSubmoduleName())) {
            $sSubmoduleName = $this->oRequest->getSubmoduleName();
        }
        $this->aFilePaths = [];
        $aReturn = [
            'controller' => '',
            'params' => [],
            'is_set' => false,
            'file_paths' => $this->aFilePaths,
            'access' => '',
        ];

        $sFilePath = $this->findFile();
        if (mb_strlen($sFilePath)) {
            $sFilePath = realpath($sFilePath);
            $aFilePath = explode(DIRECTORY_SEPARATOR, $sFilePath);
            $aControllerFileName = explode('.', array_pop($aFilePath));
            $sAccessType = mb_strtolower(array_pop($aFilePath));
            $sControllerClassName = array_shift($aControllerFileName);

            $aReturn['controller'] = vsprintf('%s%s\\%s%s', [
                $sPrefix,
                $this->oRequest->getModuleName(),
                (mb_strlen($sSubmoduleName)) ? $sSubmoduleName . '\\' : '',
                $sControllerClassName
            ]);
            $aReturn['access'] = in_array($sAccessType, ['any', 'user']) ? $sAccessType : '';
            $aReturn['params'] = $this->oRequest->getParams();
            $aReturn['is_set'] = true;
        }
        return $aReturn;
    }

    public function findFile(): string
    {
        [$sPrefix, $sBaseDir] = $this->aPrefix;
        $sSubmoduleName = '';
        if (mb_strlen($this->oRequest->getSubmoduleName())) {
            $sSubmoduleName = $this->oRequest->getSubmoduleName();
        }
        $sReturn = '';
        $this->aFilePaths = [];

        foreach ($this->aControllerDirNames as $sControllerDirName) {
            $sController = $this->getConvertedControllerName($this->oRequest->getControllerName());
            $sController = strlen($sController) ? $sController : 'Index';
            $sFilePath = $sBaseDir
                . str_replace('/', DIRECTORY_SEPARATOR, vsprintf('%s/%s/%s%s', [
                    $this->oRequest->getModuleName(),
                    $sControllerDirName,
                    mb_strlen($sSubmoduleName) ? $sSubmoduleName . DIRECTORY_SEPARATOR : '',
                    $sController
                ]))
                . '.php';

            $this->aFilePaths[] = $sFilePath;
            if (file_exists($sFilePath)) {
                $sReturn = $sFilePath;
                break;
            }

        }
        return $sReturn;
    }

    protected function getConvertedControllerName(string $sControllerName): string
    {
        $aReturn = explode($this->oRequest->getControllerNameDelimiter(), $sControllerName);
        $sReturn = '';
        foreach ($aReturn as $sV) {
            $sReturn .= mb_convert_case($sV, MB_CASE_TITLE);
        }
        $sReturn .= 'Controller';
        return $sReturn;
    }

}
