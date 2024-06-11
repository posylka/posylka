<?php

namespace app\order;

use app\core\router\Request;
use app\reference\Reference;
use app\user\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $casts = [
        'datetime' => 'datetime:d-m-Y H:i',
    ];

    protected function cityFrom(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Reference::get('cities')::getValueById((int) $value),
            set: fn (string $value) => Reference::get('cities')::getIdByValue($value),
        );
    }

    protected function cityTo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Reference::get('cities')::getValueById((int) $value),
            set: fn (string $value) => Reference::get('cities')::getIdByValue($value),
        );
    }

    public function fillByRequest(Request $request): self
    {
        $this->user_id = User::getCurrentUser()->id;
        $this->city_to = $request->post('cityTo');
        $this->city_from = $request->post('cityFrom');
        $this->datetime = $request->post('datetime');
        $this->description = $request->post('description');
        $this->photo = $request->post('photo');
        return $this;
    }
}
