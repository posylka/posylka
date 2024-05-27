<?php

namespace app\notification;

use app\user\User;

class PushNotification implements NotificationInterface
{
    public function setRecipient(User $user): void
    {
        // TODO: Implement setRecipient() method.
    }

    public function notify(string $message): bool
    {
        // TODO: Implement notify() method.
        return false;
    }
}
