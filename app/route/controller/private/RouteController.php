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
        $route = Route::query()->findOrFail($this->getParam(0))->toArray();
        /** @var User $user */
        $user = User::query()->findOrFail($route['user_id']);
        if (Tariff::check(User::getCurrentUser()?->id)) {
            $route['user_data'] = $user->getData();
        }
        return $this->response->setContent($route);
    }

    public function post(): Response
    {
        try {
            if ($this->getParam(0, false)) {
                $route = Route::query()
                    ->where('user_id', User::getCurrentUser()->id)
                    ->where('id', $this->getParam(0))
                    ->findOrFail($this->getParam(0));
            } else {
                $route = Route::query()
                    ->where('user_id', User::getCurrentUser()->id)
                    ->where('datetime', $this->request->post('datetime'))
                    ->first();
                if (!$route) {
                    $route = new Route();
                }
            }
            $route->fillByRequest($this->request)
                ->save();
            return $this->response->setMessage('ok');
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

    }

    public function hasAccess(): bool
    {
        return Tariff::check(User::getCurrentUser()?->id);
    }
}