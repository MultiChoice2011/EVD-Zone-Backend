<?php

namespace App\Enums;

enum OrderStatus
{
    const PENDING = 'pending';
    const PAID = 'paid';
    const COMPLETED = 'completed';
    const CANCELED = 'canceled';
    const RETURNED = 'returned';

    /**
     * Get wallet status list depending on app locale.
     *
     * @return array
     */
    public static function getStatusList(): array
    {
        return [
            self::PAID => trans('orderStatus.' . self::PAID),
            self::COMPLETED => trans('orderStatus.' . self::COMPLETED),
            self::CANCELED => trans('orderStatus.' . self::CANCELED),
            self::PENDING => trans('orderStatus.' . self::PENDING),
            self::RETURNED => trans('orderStatus.' . self::RETURNED),
        ];
    }


    /**
     * Get wallet status depending on app locale.
     *
     * @param $status
     * @return string
     */
    public static function getStatus($status): string
    {
        return self::getStatusList()[$status] ?? "";
    }


}
