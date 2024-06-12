<?php

namespace app\order\private;

use app\core\RestController;
use app\core\router\Response;
use app\order\Order;
use app\reference\Reference;
use app\user\User;
use Carbon\Carbon;

class ListController extends RestController
{
    public function get(): Response
    {
        $query = Order::query()->where('user_id', User::getCurrentUser()->id);
        foreach ($this->request->all() as $attr => $value) {
            switch ($attr) {
                case 'datetime_from':
                    $query->whereDate('datetime', '>=', Carbon::parse($value)->format(config('db.datetime-format')));
                    break;
                case 'datetime_to':
                    $query->whereDate('datetime', '<=', Carbon::parse($value)->format(config('db.datetime-format')));
                    break;
                case 'city_to':
                    $query->where('city_to', Reference::get('cities')->getIdByValue($value));
                    break;
                case 'city_from':
                    $query->where('city_from', Reference::get('cities')->getIdByValue($value));
                    break;
                default:
                    break;
            }
        }
        return $this->response->setContent(
            $query->paginate(config('app.pagination'), page: $this->request->get('page', 1))->items()
        );
    }
}