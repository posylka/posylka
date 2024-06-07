<?php

namespace app\user;

use app\core\db\DB;
use app\core\Session;
use app\exception\UserAlreadyExistsException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Util
{
    /**
     * @throws UserAlreadyExistsException
     */
    public static function signUp(string $username, string $password): User
    {
        if (User::query()->where('username', $username)->first()) {
            throw new UserAlreadyExistsException();
        }
        $oUser = new User();
        $oUser->username = $username;
        $oUser->password = password_hash($password, PASSWORD_BCRYPT);
        $oUser->save();
        return $oUser;
    }

    public static function login(User $user): array
    {
        if (isset($_SESSION)) {
            session_destroy();
            Session::close();
        }
        Session::start(self::getSid($user));
        $_SESSION = self::getSessionData($user);
        $act = self::generateAccessToken($user);
        $rt = self::generateRefreshToken($user);
        $tkn = Token::query()
            ->where('user_id', $user->id)
            ->where('user_agent', $_SERVER['HTTP_USER_AGENT'])
            ->first();
        if (!$tkn) {
            $tkn = new Token();
            $tkn->user_id = $user->id;
            $tkn->user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        $tkn->token = $rt;
        $tkn->save();
        return [
            'access_token' => $act,
            'refresh_token' => $rt,
        ];
    }

    public static function getSessionData(User $user): array
    {
        return [
            'user_id' => $user->id,
            'username' => $user->username,
            'phone' => $user->phone,
            'status' => $user->status,
        ];
    }
    public static function generateAccessToken(User $user): string
    {
        $payload = [
            'iss' => config('app.url'),
            'exp' => time() + config('app.access-token-lifetime'),
            'sid' => self::getSid($user),
            'uid' => $user->id,
        ];
        return JWT::encode($payload, config('app.jwt-access-key'), 'HS256');
    }

    public static function decodeAccessToken(string $jwt): array
    {
        return (array) JWT::decode($jwt, new Key(config('app.jwt-access-key'), 'HS256'));
    }
    public static function generateRefreshToken(User $user): string
    {
        $payload = [
            'iss' => config('app.url'),
            'exp' => time() + config('app.refresh-token-lifetime'),
            'sid' => self::getSid($user),
            'uid' => $user->id,
            'agent' => $_SERVER['HTTP_USER_AGENT'],
        ];
        return JWT::encode($payload, config('app.jwt-refresh-key'),  'HS512');
    }

    public static function decodeRefreshToken(string $rt): array
    {
        return (array) JWT::decode($rt, new Key(config('app.jwt-refresh-key'), 'HS512'));
    }

    public static function getSid(User $user): string
    {
        return md5($user->username) . md5($user->password);
    }

    public static function purifyPhone(string $sPhone): string
    {
        $sPhone = preg_replace('/[^0-9]+/', '', $sPhone);
        if (mb_substr($sPhone, 0, 1) === '8') {
            $sPhone = substr_replace($sPhone, '7', 0, 1);
        }
        return $sPhone;
    }
}
