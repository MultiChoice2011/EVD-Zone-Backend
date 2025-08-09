<?php
namespace App\Repositories\Admin;

use App\Models\BankCommissionSetting;
use Prettus\Repository\Eloquent\BaseRepository;
class BankCommissionSettingRepository extends BaseRepository
{
    public function store($request)
    {
        $results = [];
        foreach ($request->settings as $setting) {
            $settings = $this->model()::updateOrCreate(
                [
                    'bank_commission_id' => $request->bank_commission_id,
                    'name' => $setting['name'],
                ],
                [
                    'gate_fees' => $setting['gate_fees'],
                    'static_value' => $setting['static_value'],
                    'additional_value_fees' => $setting['additional_value_fees'],
                ]
            );
            $results[] = $settings;
        }
        return $results;
    }
    public function model(): string
    {
        return BankCommissionSetting::class;
    }
}
