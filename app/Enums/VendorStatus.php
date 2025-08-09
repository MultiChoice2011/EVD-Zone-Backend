<?php



namespace App\Enums;


enum VendorStatus:string {

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case NOT_APPROVED = 'not_approved';

    public static function getList(): array
    {
        return [
            self::getTypePending(),
            self::getTypeApproved(),
            self::getTypeNotApproved()
        ];
    }
    public static function getTypePending(): string
    {
        return self::PENDING->value;
    }
    public static function getTypeApproved(): string
    {
        return self::APPROVED->value;
    }
    public static function getTypeNotApproved(): string
    {
        return self::NOT_APPROVED->value;
    }

}
