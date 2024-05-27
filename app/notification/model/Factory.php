<?php


namespace app\notification;

class Factory
{
    public static function get(string $type): NotificationInterface
    {
        return match (mb_strtolower($type)) {
            'telegram' => new TelegramNotification(),
            'push' => new PushNotification(),
            default => new SmsNotification(),
        };
    }
}