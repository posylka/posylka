<?php

namespace app\order\private;

use app\core\RestController;
use app\core\router\Response;
use app\exception\PurchaseTariffException;
use app\order\Order;
use app\tariff\Tariff;
use app\user\User;

class OrderController extends RestController
{
    public function get(): Response
    {
        $order = Order::query()->findOrFail($this->getParam(0))->toArray();
        /** @var User $user */
        $user = User::query()->findOrFail($order['user_id']);
        if (Tariff::check(User::getCurrentUser()?->id)) {
            $order['user_data'] = $user->getData();
        }
        return $this->response->setContent($order);
    }

    public function post(): Response
    {
        try {
            $order = null;
            if ($this->getParam(0, false)) {
                $order = Order::query()
                    ->where('user_id', User::getCurrentUser()->id)
                    ->where('id', $this->getParam(0))
                    ->first();
            }
            if (!$order) {
                $order = Order::query()
                    ->where('user_id', User::getCurrentUser()->id)
                    ->where('datetime', $this->request->post('datetime'))
                    ->first();
                if (!$order) {
                    $order = new Order();
                }
            }
            $order->fillByRequest($this->request);
            $order->save();
            return $this->response->setMessage('ok');
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

    }

    public function hasAccess(): bool
    {
        return User::getCurrentUser()?->isVerified();
    }
}