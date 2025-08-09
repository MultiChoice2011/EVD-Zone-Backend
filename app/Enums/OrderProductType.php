<?php



namespace App\Enums;


enum OrderProductType:string {

    case SERIAL = 'serial';
    case TOPUP = 'topup';
    case GIFT = 'gift';

    public static function getList(): array
    {
        return [
            self::getTypeSerial(),
            self::getTypeTopUp(),
            self::getTypeGift()
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
    public static function getTypeGift(): string
    {
        return self::GIFT->value;
    }

}
