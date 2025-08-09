<?php



namespace App\Enums;


enum OrderPaymentMethod:string {

    case HYPERPAY = 'hyperpay';
    case POINT = 'point';
    case BALANCE = 'balance';
    case COD = 'cod';
    case SALLA = 'salla';

    public static function getList(): array
    {
        return [
            self::getHyperpay(),
            self::getPoint(),
            self::getBalance(),
            self::getCod(),
            self::getSalla(),
        ];
    }
    public static function getHyperpay(): string
    {
        return self::HYPERPAY->value;
    }
    public static function getPoint(): string
    {
        return self::POINT->value;
    }
    public static function getCod(): string
    {
        return self::COD->value;
    }
    public static function getBalance(): string
    {
        return self::BALANCE->value;
    }
    public static function getSalla(): string
    {
        return self::SALLA->value;
    }

}
