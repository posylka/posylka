<?php


namespace app\user\public;


use app\core\enums\HttpStatus;
use app\core\RestController;
use app\core\router\Response;
use app\exception\UserNotFoundException;
use app\exception\WrongPasswordException;
use app\user\User;
use app\user\Util;

class LoginController extends RestController
{
    protected array $validationParams = [
        'username' => 'required',
        'password' => 'required'
    ];
    public function get(): Response
    {
        return $this->response->setContent($this->validationParams)
            ->setStatusCode(HttpStatus::HTTP_OK->value)
            ->setMessage(HttpStatus::HTTP_OK->text())
            ->setIsSuccess(true);
    }

    /**
     * @throws UserNotFoundException|WrongPasswordException
     */
    public function post(): Response
    {
        /** @var User $oUser */
        $oUser = User::query()->where('username', $this->request->post('username'))->first();
        if (!$oUser) {
            throw new UserNotFoundException();
        }
        if (!password_verify($this->request->post('password'), $oUser->password)) {
            throw new WrongPasswordException();
        }
        $tokens = Util::login($oUser);
        return $this->response->setContent($tokens)
            ->setMessage('Successfully logged in.')
            ->setIsSuccess(true);
    }
}
