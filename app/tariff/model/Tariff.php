<?php

namespace app\tariff;

use app\exception\NotEnoughBalanceException;
use app\exception\PurchaseTariffException;
use app\user\User;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $table = 'tariff';
    protected $attributes = [
        'balance' => 0
    ];

    public static function check(?string $user_id): bool
    {
        if (!$user_id) return false;
        /** @var Tariff $tariff */
        $tariff = self::query()->where('user_id', $user_id)->first();
        if ($tariff) {
            $plan = Plan::get($tariff->identifier);
            if ($tariff->start_time + $plan->duration >= time()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @throws NotEnoughBalanceException|PurchaseTariffException
     */
    public function renew(): self
    {
        $plan = Plan::get($this->identifier);
        if (!$plan->canRenew) {
            throw new PurchaseTariffException();
        }
        /** @var User $user */
        $user = User::query()->findOrFail($this->user_id);
        return self::purchase($user, $plan);
    }

    /**
     * @throws NotEnoughBalanceException
     */
    public static function purchase(User $user, PlanObject $plan): Tariff
    {
        /** @var Tariff $tariff */
        $tariff = self::query()->where('user_id', $user->id)->firstOrNew();
        if ($tariff->balance < $plan->cost) {
            throw new NotEnoughBalanceException();
        }
        $tariff->user_id = $user->id;
        $tariff->identifier = $plan->identifier;
        $tariff->name = $plan->name;
        $tariff->start_time = time();
        $tariff->balance -= $plan->cost;
        $tariff->save();
        return $tariff;
    }
}