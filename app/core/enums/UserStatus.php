<?php

namespace app\core\enums;

enum UserStatus: string
{
    case VERIFIED = 'verified';
    case NOT_VERIFIED = 'not-verified';
}