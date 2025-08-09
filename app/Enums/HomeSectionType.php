<?php



namespace App\Enums;


enum HomeSectionType:string {

    case CATEGORY = 'category';
    case BANNER = 'banner';

    public static function getList(): array
    {
        return [
            self::getTypeCategory(),
            self::getTypeBanner(),
        ];
    }
    public static function getTypeCategory(): string
    {
        return self::CATEGORY->value;
    }
    public static function getTypeBanner(): string
    {
        return self::BANNER->value;
    }


}
