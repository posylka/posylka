<?php


namespace app\user\public;


use app\core\enums\HttpStatus;
use app\core\RestController;
use app\core\router\Response;
use app\exception\UserAlreadyExistsException;
use app\user\Util;

class SignUpController extends RestController
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
     * @throws UserAlreadyExistsException
     */
    public function post(): Response
    {
        $user = Util::signUp($this->request->post('username'), $this->request->post('password'));
        return $this->response->setContent(['username' => $user->username])
            ->setStatusCode(HttpStatus::HTTP_OK->value)
            ->setMessage('User successfully signed up')
            ->setIsSuccess(true);
    }

}