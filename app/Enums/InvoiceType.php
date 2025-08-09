<?php



namespace App\Enums;


enum InvoiceType:string {

    case MANUAL = 'manual';
    case AUTO = 'auto';

    public static function getList(): array
    {
        return [
            self::getTypeManual(),
            self::getTypeAuto()
        ];
    }
    public static function getTypeManual(): string
    {
        return self::MANUAL->value;
    }
    public static function getTypeAuto(): string
    {
        return self::AUTO->value;
    }

}
