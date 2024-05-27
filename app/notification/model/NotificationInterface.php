<?php

namespace app\notification;

use app\user\User;

interface NotificationInterface
{
    public function setRecipient(User $user): self;
    public function notify(string $message): bool;
}