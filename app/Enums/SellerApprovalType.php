<?php



namespace App\Enums;


enum SellerApprovalType:string {

    case PENDING = 'pending';
    case COMPLETE_PROFILE = 'complete_profile';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public static function getList(): array
    {
        return [
            self::getTypePending(),
            self::getTypeCompleteProfile(),
            self::getTypeApproved(),
            self::getTypeRejected(),
        ];
    }
    public static function getTypePending(): string
    {
        return self::PENDING->value;
    }
    public static function getTypeCompleteProfile(): string
    {
        return self::COMPLETE_PROFILE->value;
    }
    public static function getTypeApproved(): string
    {
        return self::APPROVED->value;
    }
    public static function getTypeRejected(): string
    {
        return self::REJECTED->value;
    }
}
