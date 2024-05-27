<?php

namespace app\notification;

use app\user\User;

class TelegramNotification implements NotificationInterface
{
    public function setRecipient(User $user): self
    {
        // TODO: Implement setRecipient() method.
    }

    public function notify(string $message): bool
    {
        // TODO: Implement notify() method.
        return false;
    }
}
