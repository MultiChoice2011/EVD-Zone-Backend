<?php

namespace App\Enums;

enum ProductType
{

    const REGISTERD = 'registered';
    const WAITING_PRICE_VENDOR = 'waiting_price_vendor';

    /**
     * Get wallet Type list depending on app locale.
     *
     * @return array
     */
    public static function getTypeList(): array
    {
        return [
            self::REGISTERD => trans('orderType.' . self::REGISTERD),
            self::WAITING_PRICE_VENDOR => trans('orderType.' . self::WAITING_PRICE_VENDOR),
        ];
    }


    /**
     * Get wallet Type depending on app locale.
     *
     * @param $Type
     * @return string
     */
    public static function getType($Type): string
    {
        return self::getTypeList()[$Type] ?? "";
    }

    public static function getTypes(): array
    {
        return [
            self::REGISTERD,
            self::WAITING_PRICE_VENDOR,
        ];
    }
}
