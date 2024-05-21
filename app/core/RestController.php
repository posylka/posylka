<?php


namespace app\core;

use app\core\enums\HttpStatus;
use app\core\router\Request;
use app\core\router\Response;

abstract class RestController
{
    private bool $needSession = false;
    protected int $statusCode;
    protected \app\core\router\Response $response;
    protected \app\core\router\Request $request;

    public function __construct(protected array $aParams = [])
    {
        if (IS_REQUEST && (count($_POST) == 0)) {
            $_POST = Json::decode(file_get_contents("php://input"));
        }
        $this->response = new Response();
        $this->request = Request::getInstance();
    }

    public function setNeedSession(bool $needSession): void
    {
        $this->needSession = $needSession;
    }

    public function getNeedSession(): bool
    {
        return $this->needSession;
    }

    public function get(): Response
    {
        return new Response(
            ['message' => HttpStatus::HTTP_NOT_IMPLEMENTED->text()],
            HttpStatus::HTTP_NOT_IMPLEMENTED->value
        );
    }

    public function post(): Response
    {
        return new Response(
            ['message' => HttpStatus::HTTP_NOT_IMPLEMENTED->text()],
            HttpStatus::HTTP_NOT_IMPLEMENTED->value
        );
    }

    public function put(): Response
    {
        return new Response(
            ['message' => HttpStatus::HTTP_NOT_IMPLEMENTED->text()],
            HttpStatus::HTTP_NOT_IMPLEMENTED->value
        );
    }

    public function delete(): Response
    {
        return new Response(
            ['message' => HttpStatus::HTTP_NOT_IMPLEMENTED->text()],
            HttpStatus::HTTP_NOT_IMPLEMENTED->value
        );
    }

    final public function getParam(int $id, $default = '')
    {
        return Util::getParam($this->aParams, $id, $default);
    }

    protected function hasAccess(): bool
    {
        return true;
    }

    public function canGet(): bool
    {
        return $this->hasAccess();
    }

    public function canPost(): bool
    {
        return $this->hasAccess();
    }

    public function canPut(): bool
    {
        return $this->hasAccess();
    }

    public function canDelete(): bool
    {
        return $this->hasAccess();
    }
}
