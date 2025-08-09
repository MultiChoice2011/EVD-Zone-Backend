<?php



namespace App\Enums;


enum BrandImageKey:string {

    case LOGO = 'logo';
    case H_IMAGE = 'h_image';
    case V_IMAGE = 'v_image';

    public static function getList(): array
    {
        return [
            self::getTypeLogo(),
            self::getTypeHImage(),
            self::getTypeVImage(),
        ];
    }
    public static function getTypeLogo(): string
    {
        return self::LOGO->value;
    }
    public static function getTypeHImage(): string
    {
        return self::H_IMAGE->value;
    }
    public static function getTypeVImage(): string
    {
        return self::V_IMAGE->value;
    }

}
