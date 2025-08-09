<?php
namespace App\Repositories\Admin;

use App\Models\BankCommission;
use Prettus\Repository\Eloquent\BaseRepository;
class BankCommissionRepository extends BaseRepository
{
    public function getBankCommissions()
    {
        return $this->model
            ->with('translations','settings')
            ->Active()
            ->get();
    }
    public function model(): String
    {
        return BankCommission::class;
    }
}
