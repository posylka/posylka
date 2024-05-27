<?php

namespace app\notification;

use app\user\User;

interface NotificationInterface
{
    public function setRecipient(User $user): void;
    public function notify(string $message): bool;
}