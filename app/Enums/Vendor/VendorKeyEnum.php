<?php



namespace App\Enums\Vendor;


enum VendorKeyEnum:string {

    case ACCOUNT_ID = 'account_id';
    case PHONE = 'phone';
    case SERVER_ID = 'server_id';

    public static function getList(): array
    {
        return [
            self::getAccountIdKey(),
            self::getPhoneKey(),
            self::getServerIdKey(),
        ];
    }
    public static function getAccountIdKey(): string
    {
        return self::ACCOUNT_ID->value;
    }
    public static function getPhoneKey(): string
    {
        return self::PHONE->value;
    }
    public static function getServerIdKey(): string
    {
        return self::SERVER_ID->value;
    }

}
