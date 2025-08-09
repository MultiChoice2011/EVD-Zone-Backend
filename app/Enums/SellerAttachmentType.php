<?php



namespace App\Enums;


enum SellerAttachmentType:string {

    case IDENTITY = 'identity';
    case COMMERCIAL_REGISTER = 'commercial_register';
    case TAX_CARD = 'tax_card';
    case MORE = 'more';

    public static function getList(): array
    {
        return [
            self::getTypeIdentity(),
            self::getTypeCommercialRegister(),
            self::getTypeTaxCard(),
            self::getTypeMore(),
        ];
    }
    public static function getTypeIdentity(): string
    {
        return self::IDENTITY->value;
    }
    public static function getTypeCommercialRegister(): string
    {
        return self::COMMERCIAL_REGISTER->value;
    }
    public static function getTypeTaxCard(): string
    {
        return self::TAX_CARD->value;
    }
    public static function getTypeMore(): string
    {
        return self::MORE->value;
    }
}
