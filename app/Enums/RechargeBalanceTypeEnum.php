<?php
namespace App\Enums;

enum RechargeBalanceTypeEnum
{
    const CASH = 'cash';
    const VISA = 'visa';
    const MADA = 'mada';
    const ADDED_BY_ADMIN = 'add-by-admin';
    const WITHDRAW_BY_ADMIN = 'withdraw_by_admin';
}
