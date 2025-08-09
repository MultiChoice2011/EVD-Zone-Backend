<?php



namespace App\Enums;


enum MobileSectionStyle:string {

    case ST1 = 'st1';
    case ST2 = 'st2';
    case ST3 = 'st3';

    public static function getList(): array
    {
        return [
            self::getSt1(),
            self::getSt2(),
            self::getSt3(),
        ];
    }
    public static function getSt1(): string
    {
        return self::ST1->value;
    }
    public static function getSt2(): string
    {
        return self::ST2->value;
    }
    public static function getSt3(): string
    {
        return self::ST3->value;
    }

}
