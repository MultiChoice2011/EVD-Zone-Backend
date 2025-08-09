<?php

namespace App\Enums;

enum OrderSource:string
{
    case WEB = 'web';
    case MOBILE = 'mobile';
    case DASHBOARD = 'dashboard';

    public static function getList(): array
    {
        return [
            self::getSourceWeb(),
            self::getSourceMobile(),
            self::getSourceDashboard()
        ];
    }
    public static function getSourceWeb(): string
    {
        return self::WEB->value;
    }
    public static function getSourceMobile(): string
    {
        return self::MOBILE->value;
    }
    public static function getSourceDashboard(): string
    {
        return self::DASHBOARD->value;
    }


}
