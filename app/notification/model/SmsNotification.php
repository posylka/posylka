<?php

namespace app\notification;

use app\user\User;
use app\user\Util;
use Mobizon\Mobizon_ApiKey_Required;
use Mobizon\Mobizon_Curl_Required;
use Mobizon\Mobizon_Error;
use Mobizon\Mobizon_Http_Error;
use Mobizon\Mobizon_OpenSSL_Required;
use Mobizon\Mobizon_Param_Required;
use Mobizon\MobizonApi;

class SmsNotification implements NotificationInterface
{
    private string $phone;
    public function setRecipient(User $user): self
    {
        $this->phone = Util::purifyPhone($user->phone);
        return $this;
    }

    /**
     * @throws Mobizon_Error
     * @throws Mobizon_ApiKey_Required
     * @throws Mobizon_Param_Required
     * @throws Mobizon_Curl_Required
     * @throws Mobizon_OpenSSL_Required
     * @throws Mobizon_Http_Error
     */
    public function notify(string $message): bool
    {
        $api = new MobizonApi(config('mobizon.apiKey'), config('mobizon.apiServer'));
        if ($api->call(
                'message',
                'sendSMSMessage',
                ['recipient' => $this->phone, 'text' => $message,]
            )) {
            /**
             * todo logger
             * $messageId = $api->getData('messageId');
             * echo 'Message created with ID:' . $messageId . PHP_EOL;
             *
             * if ($messageId) {
             * echo 'Get message info...' . PHP_EOL;
             * $messageStatuses = $api->call(
             * 'message',
             * 'getSMSStatus',
             * array(
             * 'ids' => array($messageId, '13394', '11345', '4393')
             * ),
             * array(),
             * true
             * );
             *
             * if ($api->hasData()) {
             * foreach ($api->getData() as $messageInfo) {
             * echo 'Message # ' . $messageInfo->id . " status:\t" . $messageInfo->status . PHP_EOL;
             * }
             * }
             * }
             */
            return true;
        }
        /**
         * todo logger
         * echo 'An error occurred while sending message: [' . $api->getCode() . '] ' . $api->getMessage() . 'See details below:' . PHP_EOL;
         */
        return false;
    }
}
