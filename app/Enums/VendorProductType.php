<?php



namespace App\Enums;


enum VendorProductType:string {

    case SERIAL = 'serial';
    case TOPUP = 'topup';

    public static function getList(): array
    {
        return [
            self::getTypeSerial(),
            self::getTypeTopUp()
        ];
    }
    public static function getTypeSerial(): string
    {
        return self::SERIAL->value;
    }
    public static function getTypeTopUp(): string
    {
        return self::TOPUP->value;
    }

}
