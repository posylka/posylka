<?php

namespace app\user;

use app\core\enums\UserStatus;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $attributes = [
        'phone' => '',
        'status' => UserStatus::NOT_VERIFIED
    ];

    public static function getCurrentUser(): ?User
    {
        $user = null;
        if (i($_SESSION, 'user_id')) {
            $user = User::query()->find($_SESSION['user_id']);
        }
        return $user;
    }
}
