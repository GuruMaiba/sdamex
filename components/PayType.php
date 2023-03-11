<?php

namespace app\components;

class PayType
{
    public const COURSE = 1;
    public const WEBINAR = 2;
    public const LESSON = 3;

    public static function getTypesArr() {
        return [
            self::COURSE => 'Курсы',
            self::WEBINAR => 'Вебинары',
            self::LESSON => 'Уроки',
        ];
    }

    public static function getTypeLable($type, $default = null) {
        $constArr = self::getTypesArr();
        return isset($constArr[$type]) ? $constArr[$type] : $default;
    }
}
