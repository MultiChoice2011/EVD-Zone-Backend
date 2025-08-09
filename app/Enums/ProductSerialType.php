<?php



namespace App\Enums;


enum ProductSerialType:string {

    case HOLD = 'hold';
    case FREE = 'free';
    case PRESOLD = 'presold';
    case SOLD = 'sold';
    case REFUND = 'refund';
    case STOPPED = 'stopped';
    case REFUSED = 'refused';

    public static function getList(): array
    {
        return [
            self::getTypeHold(),
            self::getTypeFree(),
            self::getTypePresold(),
            self::getTypeSold(),
            self::getTypeRefund(),
            self::getTypeStopped(),
            self::getTypeRefused(),
        ];
    }
    public static function getTypeHold(): string
    {
        return self::HOLD->value;
    }
    public static function getTypeFree(): string
    {
        return self::FREE->value;
    }
    public static function getTypePresold(): string
    {
        return self::PRESOLD->value;
    }
    public static function getTypeSold(): string
    {
        return self::SOLD->value;
    }
    public static function getTypeRefund(): string
    {
        return self::REFUND->value;
    }
    public static function getTypeStopped(): string
    {
        return self::STOPPED->value;
    }
    public static function getTypeRefused(): string
    {
        return self::REFUSED->value;
    }
}
