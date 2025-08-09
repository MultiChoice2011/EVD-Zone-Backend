<?php



namespace App\Enums;


enum HyperpayPaymentBrand:string {

    case MADA = 'MADA';
    case VISA = 'VISA';
    case MASTER = 'MASTER';

    public static function getList(): array
    {
        return [
            self::getMADA(),
            self::getVISA(),
            self::getMASTER(),
        ];
    }
    public static function getMADA(): string
    {
        return self::MADA->value;
    }
    public static function getVISA(): string
    {
        return self::VISA->value;
    }
    public static function getMASTER(): string
    {
        return self::MASTER->value;
    }

}
