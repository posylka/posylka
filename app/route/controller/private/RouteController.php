<?php

namespace app\route\private;

use app\core\RestController;
use app\core\router\Response;
use app\route\Route;
use app\tariff\Tariff;
use app\user\User;

class RouteController extends RestController
{
    public function get(): Response
    {
        if ($this->getParam(0, false)) {
            $this->response->setContent(
                Route::query()
                ->where('user_id', User::getCurrentUser()?->id)
                ->where('id', $this->getParam(0))
                ->firstOrFail()->toArray()
            );
        } else {
            $a = [];
            foreach (Route::query()
                     ->where('user_id', User::getCurrentUser()->id)
                     ->lazyById(10) as $route) {
                $a[$route->id] = $route->toArray();
            }
            $this->response->setContent($a);
        }
        return $this->response;
    }

    public function post(): Response
    {
        try {
            if ($this->getParam(0, false)) {
                $route = Route::query()->findOrFail($this->getParam(0));
            } else {
                $route = Route::query()
                    ->where('user_id', User::getCurrentUser()->id)
                    ->where('datetime', $this->request->post('datetime'))
                    ->first();
                if (!$route) {
                    $route = new Route();
                }
            }
            $route->fillByRequest($this->request);
            $route->save();
            return $this->response->setMessage('ok');
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

    }

    public function hasAccess(): bool
    {
        $user = User::getCurrentUser();
        if (!$user) return false;
        /** @var Tariff $tariff */
        $tariff = Tariff::query()->where('user_id', $user->id)->firstOrNew();
        return $tariff->check();
    }
}