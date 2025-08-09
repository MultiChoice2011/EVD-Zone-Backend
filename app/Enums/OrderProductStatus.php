<?php



namespace App\Enums;


enum OrderProductStatus:string {

    case WAITING = 'waiting';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public static function getList(): array
    {
        return [
            self::getTypeWaiting(),
            self::getTypeInProgress(),
            self::getTypeCompleted(),
            self::getTypeRejected()
        ];
    }
    public static function getTypeWaiting(): string
    {
        return self::WAITING->value;
    }
    public static function getTypeInProgress(): string
    {
        return self::IN_PROGRESS->value;
    }
    public static function getTypeCompleted(): string
    {
        return self::COMPLETED->value;
    }
    public static function getTypeRejected(): string
    {
        return self::REJECTED->value;
    }

}
