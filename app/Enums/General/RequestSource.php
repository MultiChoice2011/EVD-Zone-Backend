<?php



namespace App\Enums\General;


enum RequestSource:string {

    case WEB = 'web';
    case MOBILE = 'mobile';

    public static function getList(): array
    {
        return [
            self::getSourceWeb(),
            self::getSourceMobile(),
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

}
