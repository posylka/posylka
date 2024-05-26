<?php

namespace app\user;

use app\core\enums\UserStatus;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $attributes = [
        'status' => UserStatus::NOT_VERIFIED
    ];
}
