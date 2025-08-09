<?php



namespace App\Enums;


enum FirebaseTopicEnum:string {

    case ALL_ADMINS = 'all-admins';
    case ADMIN = 'admin-';
    case ALL_SELLERS = 'all-sellers';
    case SELLER = 'seller-';

    public static function getList(): array
    {
        return [
            self::getAllAdminsTopic(),
            self::getAdminTopic(),
            self::getAllSellersTopic(),
            self::getSellerTopic(),
        ];
    }
    public static function getAllAdminsTopic(): string
    {
        return self::ALL_ADMINS->value;
    }
    public static function getAdminTopic(): string
    {
        return self::ADMIN->value;
    }
    public static function getAllSellersTopic(): string
    {
        return self::ALL_SELLERS->value;
    }
    public static function getSellerTopic(): string
    {
        return self::SELLER->value;
    }

}
