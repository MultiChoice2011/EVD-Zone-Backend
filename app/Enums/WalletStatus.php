<?php

namespace App\Enums;

enum WalletStatus:string
{
    case PENDING = 'pending';
    case COMPLETE = 'complete';
    case REFUSED = 'refused';

    public static function getList(): array
    {
        return [
            self::getStatusPending(),
            self::getStatusComplete(),
            self::getStatusRefused()
        ];
    }
    public static function getStatusPending(): string
    {
        return self::PENDING->value;
    }
    public static function getStatusComplete(): string
    {
        return self::COMPLETE->value;
    }
    public static function getStatusRefused(): string
    {
        return self::REFUSED->value;
    }


}
