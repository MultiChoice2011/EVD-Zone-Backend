<?php



namespace App\Enums;


enum SellerGroupConditionType:string {

    case ORDERS_NUMBER = 'orders_number';
    case ORDERS_AMOUNT = 'orders_amount';
    case CREATED_AT = 'created_at';
    case REGION_ID = 'region_id';

    public static function getList(): array
    {
        return [
            self::getTypeOrdersNumber(),
            self::getTypeOrdersAmount(),
            self::getTypeCreatedAt(),
            self::getTypeRegionId(),
        ];
    }
    public static function getTypeOrdersNumber(): string
    {
        return self::ORDERS_NUMBER->value;
    }
    public static function getTypeOrdersAmount(): string
    {
        return self::ORDERS_AMOUNT->value;
    }
    public static function getTypeCreatedAt(): string
    {
        return self::CREATED_AT->value;
    }
    public static function getTypeRegionId(): string
    {
        return self::REGION_ID->value;
    }
}
