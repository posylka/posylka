<?php


namespace app\core;

use app\core\enums\HttpStatus;
use app\core\router\Request;
use app\core\router\Response;
use app\core\validation\Rule;
use app\core\validation\rules\RuleInterface;

abstract class RestController
{
    private bool $needSession = false;
    protected int $statusCode;
    protected \app\core\router\Response $response;
    protected \app\core\router\Request $request;

    protected array $aErrors = [];

//        $validationParams = [
//            'phone' => 'required|kz-phone-number',   delimeter -> |
//            'date-from' => 'required|date-min:17.01.2024|date-max:"Monday next week"', delimeter -> |, param -> :
//        ];
    protected array $validationParams = [];

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

    public function validateRequest(): bool
    {
//        example
//        $validationParams = [
//            'phone' => 'required|kz-phone-number',   delimeter - |
//            'date-from' => 'required|date-min:17.01.2024|date-max:"Monday next week"', delimeter - |, param - :
//        ];
        if ($this->request->method() !== 'GET') {
            $aRules = Rule::$rules;
            foreach ($this->validationParams as $field => $rules) {
                foreach (explode('|', $rules) as $rule) {
                    if (isset($aRules[$rule])) {
                        $validator = new $aRules[$rule]();
                        $params = [];
                        if (str_contains($rule, ':')) {
                            $tmp = explode(':', $rule);
                            $params = i($tmp, 1);
                        }
                        if (!$validator->validate(i($this->request->all(), $field), $params)) {
                            $this->aErrors[$field] = sprintf('Validation error, field = %s, rule = %s', $field, $rule);
                        }
                    }
                }
            }
            if (count($this->aErrors)) {
                return false;
            }
        }
        return true;
    }

    public function getErrors(): array
    {
        return $this->aErrors;
    }
}
