<?php



namespace App\Enums;


enum WebSectionStyle:string {

    case ST1 = 'st1';
    case ST2 = 'st2';
    case ST3 = 'st3';
    case ST4 = 'st4';
    case ST5 = 'st5';

    public static function getList(): array
    {
        return [
            self::getSt1(),
            self::getSt2(),
            self::getSt3(),
            self::getSt4(),
            self::getSt5(),
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
    public static function getSt4(): string
    {
        return self::ST4->value;
    }
    public static function getSt5(): string
    {
        return self::ST5->value;
    }

}
