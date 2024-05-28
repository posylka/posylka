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

    public function check(): bool
    {
        $plan = Plan::get($this->identifier);
        if ($this->start_time + $plan->duration < time()) {
            try {
                $this->renew();
            } catch (\Exception $exception) {
                return false;
            }
        }
        return true;
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