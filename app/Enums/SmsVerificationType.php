<?php



namespace App\Enums;

use App\Services\General\SmsVerification\MsegatService;
use App\Services\General\SmsVerification\SmsMisrService;

enum SmsVerificationType:string {

    case MSEGATINTEGRATION = 'msegat';
    case SMSMISRINTEGRATION = 'smsmisr';

    public static function getList(): array
    {
        return [
            self::getTypeMsegat(),
            self::getTypeSmsMisr(),
        ];
    }
    public static function getTypeMsegat(): string
    {
        return self::MSEGATINTEGRATION->value;
    }
    public static function getTypeSmsMisr(): string
    {
        return self::SMSMISRINTEGRATION->value;
    }
}
