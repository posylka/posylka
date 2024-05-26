<?php


namespace app\user\public;


use app\core\RestController;
use app\core\router\Response;
use app\exception\InvalidTokenException;
use app\exception\UserNotFoundException;
use app\user\Token;
use app\user\User;
use app\user\Util;

class RefreshTokenController extends RestController
{
    /**
     * @throws UserNotFoundException|InvalidTokenException
     */
    public function get(): Response
    {
        $rt = i($_SERVER, 'HTTP_JWT_REFRESH');
        $decoded = Util::decodeRefreshToken($rt);

        /** @var User $user */
        $user = User::query()->where('id', $decoded['uid'])->first();

        if (!$user) {
            throw new UserNotFoundException();
        }

        $tkn = Token::query()
            ->where('user_id', $decoded['uid'])
            ->where('token', $rt)
            ->first();

        if (
            !$tkn
            || Util::getSid($user) !== $decoded['sid']
            || $decoded['agent'] !== $_SERVER['HTTP_USER_AGENT']
        ) {
            throw new InvalidTokenException();
        }

        $tokens = Util::login($user);
        return $this->response->setContent($tokens)
            ->setMessage('Tokens successfully refreshed.')
            ->setIsSuccess(true);
    }
}
