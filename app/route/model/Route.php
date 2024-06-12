<?php

namespace app\route;

use app\core\router\Request;
use app\exception\UserAlreadyExistsException;
use app\reference\Reference;
use app\user\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'routes';
    protected $casts = [
        'datetime' => 'datetime:d-m-Y H:i',
        'user_data' => 'array',
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

    protected function renew(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => RenewEnum::getTextByValue($value),
            set: fn (string $value) => RenewEnum::getValueByText($value),
        );
    }

    public function fillByRequest(Request $request): self
    {
        $this->user_id = User::getCurrentUser()->id;
        $this->city_to = $request->post('cityTo');
        $this->city_from = $request->post('cityFrom');
        $this->description = $request->post('description');
        $this->renew = $request->post('renew');
        $this->datetime = $request->post('datetime');
        return $this;
    }
}
