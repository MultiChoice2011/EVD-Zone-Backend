<?php

namespace App\Enums;

enum CustomerDeviceType:string
{

    case WEB = 'web';
    case PHONE = 'phone';

    /**
     * Get wallet Type list depending on app locale.
     *
     * @return array
     */
    public static function getList(): array
    {
        return [
            self::getTypeWeb(),
            self::getTypePhone()
        ];
    }


    public static function getTypeWeb(): string
    {
        return self::WEB->value;
    }

    public static function getTypePhone(): string
    {
        return self::PHONE->value;
    }
}
