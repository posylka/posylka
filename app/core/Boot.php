<?php


namespace app\core;

use app\core\db\DatabaseProvider;
use app\core\enums\HttpStatus;
use app\core\router\Dispatcher;
use app\core\router\Request;
use app\core\router\Response;
use app\core\router\RoutingHelper;
use app\exception\HttpExceptionInterface;

final class Boot
{
    private ?string $sJwtError = null;
    public function __construct()
    {
        date_default_timezone_set(config('app.timezone'));
        define('PROD', config('app.site_url') === 'https://posylka.kz');
        ob_start();
        try {
            if ($this->needInitSession()) {
                \app\core\Session::start();
            }
            if ($this->sJwtError) {
                $response = new Response();
                $response->setStatusCode(HttpStatus::HTTP_UNAUTHORIZED->value)
                    ->setMessage($this->sJwtError)
                    ->setIsSuccess(false)
                    ->send();
            } else {
                DatabaseProvider::init();
                $this->corsHook();
                $this->route();
            }
        } catch (HttpExceptionInterface $exception) {
            $response = new Response();
            $response->setIsSuccess(false)
                ->setStatusCode($exception->getHttpCode()->value)
                ->setMessage($exception->getHttpMessage());
            if (!PROD) {
                $response->setContent($exception->getTrace());
            }
            $response->send();
        } catch (\Throwable $throwable) {
           // Logger::log($throwable); todo logger
            $response = new Response();
            $response->setIsSuccess(false)
                ->setStatusCode(\app\core\enums\HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value)
                ->setMessage($throwable->getMessage());
            if (!PROD) {
                $response->setContent($throwable->getTrace());
            }
            $response->send();
        }
        Session::close();
    }

    private function route(): void
    {
        $appPath = WWW_PATH . DIRECTORY_SEPARATOR . 'app';
        $path = $_GET['APP_PATH'] ?? '/';
        $oRoutingHelper = RoutingHelper::getInstance();
        $oRoutingHelper->setBaseDir($appPath)->addControllerDirName('controller')->setPrefixChar('_');

        $oR = new Request();
        $oR->setPath($path)
            ->setHelper($oRoutingHelper)
            ->setControllerNameDelimiter('-')
            ->process();
        $oDispatcher = new Dispatcher();
        $oDispatcher->setPrefix('app', $appPath)->addControllerDirName('controller');

        $oController = $oDispatcher->handle($oR);

        $response = new Response();
        if ($oController->getNeedSession() && !($_SESSION['user_id'] ?? false)) {
            $response->setStatusCode(HttpStatus::HTTP_UNAUTHORIZED->value)
                ->setMessage($this->sJwtError ?? HttpStatus::HTTP_UNAUTHORIZED->text())
                ->setIsSuccess(false);
        } elseif (IS_OPTIONS) {
            $response = new Response();
        } elseif (!$oController->validateRequest()) {
            $response->setStatusCode(HttpStatus::HTTP_BAD_REQUEST->value)
                ->setContent($oController->getErrors())
                ->setMessage(HttpStatus::HTTP_BAD_REQUEST->text())
                ->setIsSuccess(false);
        } else {
            switch($oR->method()) {
                case 'GET':
                    if ($oController->canGet()) {
                        $response = $oController->get();
                    } else {
                        $response = $this->getForbiddenResponse();
                    }
                    break;
                case 'POST':
                    if ($oController->canPost()) {
                        $_POST = Json::decode(file_get_contents("php://input"));
                        $response = $oController->post();
                    } else {
                        $response = $this->getForbiddenResponse();
                    }
                    break;
                case 'PUT':
                    if ($oController->canPut()) {
                        $_PUT = Json::decode(file_get_contents("php://input"));
                        $response = $oController->put();
                    } else {
                        $response = $this->getForbiddenResponse();
                    }
                    break;
                case 'DELETE':
                    if ($oController->canDelete()) {
                        $_DELETE = Json::decode(file_get_contents("php://input"));
                        $response = $oController->delete();
                    } else {
                        $response = $this->getForbiddenResponse();
                    }
                    break;
                default:
                    $response = new Response([], HttpStatus::HTTP_METHOD_NOT_ALLOWED->value);
                    $response->setMessage(HttpStatus::HTTP_METHOD_NOT_ALLOWED->text())
                        ->setIsSuccess(false);
                    break;
            }
        }
        $response->send();
    }

    private function getForbiddenResponse(): Response
    {
        $response = new Response([], HttpStatus::HTTP_FORBIDDEN->value);
        return $response->setMessage(HttpStatus::HTTP_FORBIDDEN->text())
            ->setIsSuccess(false);
    }
    private function needInitSession(): bool
    {
        if (isset($_SERVER['HTTP_JWT_ACCESS'])) {
            try {
                $decoded = \app\user\Util::decodeAccessToken($_SERVER['HTTP_JWT_ACCESS']);
            } catch (\Exception $exception) {
                $this->sJwtError = $exception->getMessage();
            }
        }
        return (isset($decoded['sid']) && $decoded['sid'] != '');
    }

    /**
     * Хук для CORS
     */
    private function corsHook(): void
    {
        $allowedOrigin = config('app.site_url');
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            if (in_array($_SERVER['HTTP_ORIGIN'], config('app.other_allowed_urls'))) {
                $allowedOrigin = $_SERVER['HTTP_ORIGIN'];
            }
            $urlPieces = parse_url($_SERVER['HTTP_ORIGIN']);
            $domain = $urlPieces['host'] ?? '';
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
                foreach (config('app.other_allowed_urls') as $url) {
                    if (sprintf('*.%s', $regs['domain']) == $url) {
                        $allowedOrigin = $_SERVER['HTTP_ORIGIN'];
                    }
                }
            }
        }

        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: content-type,accept,x-file-name,x-requested-with,content-range,content-disposition,content-description,authorization,browser-id, access-key, access-token');
        header('Access-Control-Expose-Headers: content-disposition');

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            exit;
        }
    }
}
