<?php

namespace App\Enums;

enum GeneralStatusEnum:string
{

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    /**
     * Get wallet Type list depending on app locale.
     *
     * @return array
     */
    public static function getList(): array
    {
        return [
            self::getStatusActive(),
            self::getStatusInactive()
        ];
    }


    public static function getStatusActive(): string
    {
        return self::ACTIVE->value;
    }

    public static function getStatusInactive(): string
    {
        return self::INACTIVE->value;
    }
}
