<?php

namespace app\reference;

use app\core\Locale;
use Illuminate\Database\Eloquent\Model;

class CitiesReference extends Model implements ReferenceInterface
{
    protected $table = 'cities_reference';
    protected $fillable = ['ru', 'kz'];
    public static function getValueById(int $id): string
    {
        $lang = Locale::getInstance()->getLang();
        return CitiesReference::query()->findOrFail($id)->$lang;
    }

    public static function getIdByValue(string $value): int
    {
        $lang = Locale::getInstance()->getLang();
        return CitiesReference::query()->where($lang, $value)->firstOrFail()->id;
    }
}
