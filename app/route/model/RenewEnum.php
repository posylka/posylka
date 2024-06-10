<?php

namespace app\route;

use app\core\enums\EnumToArray;
use app\core\locale\Locale;

enum RenewEnum: string
{
   // use EnumToArray;

    case EVERYDAY = 'every_day';
    case EVERY2DAYS = 'every_2_days';
    case EVERYWEEK = 'every_week';
    case EVERYMONTH = 'every_month';
    case EVERYMONDAY = 'every_monday';
    case EVERYTUESDAY = 'every_tuesday';
    case EVERYWEDNESDAY = 'every_wednesday';
    case EVERYTHURSDAY = 'every_thursday';
    case EVERYFRIDAY = 'every_friday';
    case EVERYSATURDAY = 'every_saturday';
    case EVERYSUNDAY = 'every_sunday';
    case DONOTRENEW = 'do_not_renew';

    public static function getTextByValue(string $value): string
    {
        return Locale::getInstance()->translate($value);
    }

    public static function getValueByText(string $text): string
    {
        $translations = Locale::getInstance()->getTranslations();
        foreach ($translations as $key => $value) {
            if ($value === $text) {
                return $key;
            }
        }
        return 'do_not_renew'; //default value
    }


}