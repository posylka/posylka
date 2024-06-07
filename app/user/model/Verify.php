<?php

namespace app\user;

use app\core\Json;
use app\exception\CodeExpiredException;
use Illuminate\Database\Eloquent\Model;

class Verify extends Model
{
    private static bool $verified = false;
    protected $table = 'verify';

    public function verify(string $sCode): bool
    {
        if (strtotime($this->updated_at) + config('app.verify-code-lifetime') < time()) {
            throw new CodeExpiredException();
        }
        if ($this->code === $sCode) {
            self::$verified = true;
            return true;
        }
        return false;
    }

    public function execute(): bool
    {
        if (!self::$verified) {
            return false;
        }
        /** @var Model|null $target */
        $target = $this->classname::query()->findOrFail($this->instance_id);
        $data = Json::decode($this->data);
        foreach ($data as $attr => $val) {
            $target->$attr = $val;
        }
        $target->save();
        $this->delete();
        return true;
    }

    public function blank(string $user_id, string $classname, string $instance_id, array $data, int $code): void
    {
        $this->user_id = $user_id;
        $this->classname = $classname;
        $this->instance_id = $instance_id;
        $this->data = Json::encode($data);
        $this->code = $code;
    }
}