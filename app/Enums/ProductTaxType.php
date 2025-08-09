<?php



namespace App\Enums;


enum ProductTaxType:string {

    case INCLUDED = 'included';
    case PARTIAL = 'partial';

    public static function getList(): array
    {
        return [
            self::getTypeIncluded(),
            self::getTypePartial(),
        ];
    }
    public static function getTypeIncluded(): string
    {
        return self::INCLUDED->value;
    }
    public static function getTypePartial(): string
    {
        return self::PARTIAL->value;
    }

}
