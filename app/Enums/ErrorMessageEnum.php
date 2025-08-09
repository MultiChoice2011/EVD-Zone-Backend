<?php
namespace App\Enums;
enum ErrorMessageEnum
{
    const ALREADY_USED = 'already_used';
    const CODE_INCORRECT = 'code_incorrect';
    const INEFFICIENT = 'inefficient';
    const OTHER = 'other';
    public static function values(): array
    {
        return [
            self::ALREADY_USED,
            self::CODE_INCORRECT,
            self::INEFFICIENT,
            self::OTHER,
        ];
    }
}
