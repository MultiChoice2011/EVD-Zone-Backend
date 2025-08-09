<?php



namespace App\Enums;


enum OrderComplaintStatus:string {

    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public static function getList(): array
    {
        return [
            self::getTypeInProgress(),
            self::getTypeCompleted(),
        ];
    }

    public static function getTypeInProgress(): string
    {
        return self::IN_PROGRESS->value;
    }
    public static function getTypeCompleted(): string
    {
        return self::COMPLETED->value;
    }

}
