<?php

namespace app\core\enums;

enum MigrationStatus: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WAITING = 'waiting';
}