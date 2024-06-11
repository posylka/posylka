<?php

namespace app\order\private;

use app\core\RestController;
use app\core\router\Response;
use app\order\Order;
use app\user\User;

class OrderController extends RestController
{
    public function get(): Response
    {
        if ($this->getParam(0, false)) {
            $this->response->setContent(
                Order::query()
                    ->where('user_id', User::getCurrentUser()?->id)
                    ->where('id', $this->getParam(0))
                    ->firstOrFail()->toArray()
            );
        } else {
            $a = [];
            foreach (Order::query()
                         ->where('user_id', User::getCurrentUser()->id)
                         ->lazyById(10) as $order) {
                $a[$order->id] = $order->toArray();
            }
            $this->response->setContent($a);
        }
        return $this->response;
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