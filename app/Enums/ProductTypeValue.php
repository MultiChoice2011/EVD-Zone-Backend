<?php

namespace App\Enums;

enum ProductTypeValue:string
{
    //    case PHYSICAL = 'physical';
    case DIGITAL = 'digital';
    case TOPUP = 'topup';

    /**
     * Get wallet Type list depending on app locale.
     *
     * @return array
     */
    public static function getList(): array
    {
        return [
            self::getTypeDigital(),
//            self::getTypePhysical(),
            self::getTypeTopup(),
        ];
    }


    public static function getTypeDigital(): string
    {
        return self::DIGITAL->value;
    }

//    public static function getTypePhysical(): string
//    {
//        return self::PHYSICAL->value;
//    }

    public static function getTypeTopup(): string
    {
        return self::TOPUP->value;
    }
}
