<?php

namespace App\Repositories\Admin;

use App\Models\DirectPurchase;
use App\Models\DirectPurchasePriority;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class DirectPurchasePriorityRepository extends BaseRepository
{
    public function __construct(
        Application                          $app,
    )
    {
        parent::__construct($app);
    }

    public function showByLevel($requestData, $directPurchaseId)
    {
        return $this->model
            ->where('direct_purchase_id', $directPurchaseId)
            ->where('vendor_id', '!=', $requestData->vendor_id)
            ->where('priority_level', $requestData->priority_level)
            ->first();
    }

    public function showByVendor($requestData, $directPurchaseId)
    {
        return $this->model
            ->where('direct_purchase_id', $directPurchaseId)
            ->where('vendor_id', $requestData->vendor_id)
            ->first();
    }

    public function store($requestData, $directPurchaseId)
    {
        $DirectPurchasePriorityByLevel = $this->showByLevel($requestData, $directPurchaseId);
        $DirectPurchasePriorityByVendor = $this->showByVendor($requestData, $directPurchaseId);

        if ($DirectPurchasePriorityByLevel && $DirectPurchasePriorityByVendor) {
            $DirectPurchasePriorityByLevel->priority_level = $DirectPurchasePriorityByVendor->priority_level;
            $DirectPurchasePriorityByLevel->save();
            $DirectPurchasePriorityByVendor->priority_level = $requestData->priority_level;
            $DirectPurchasePriorityByVendor->save();

        }elseif ($DirectPurchasePriorityByLevel && !$DirectPurchasePriorityByVendor) {
            $DirectPurchasePriorityByLevel->delete();
            $this->model->create([
                'direct_purchase_id' => $directPurchaseId,
                'vendor_id' => $requestData->vendor_id,
                'priority_level' => $requestData->priority_level,
            ]);

        }elseif (!$DirectPurchasePriorityByLevel && $DirectPurchasePriorityByVendor) {
            $DirectPurchasePriorityByVendor->priority_level = $requestData->priority_level;
            $DirectPurchasePriorityByVendor->save();

        }elseif (!$DirectPurchasePriorityByLevel && !$DirectPurchasePriorityByVendor) {
            $this->model->create([
                'direct_purchase_id' => $directPurchaseId,
                'vendor_id' => $requestData->vendor_id,
                'priority_level' => $requestData->priority_level,
            ]);
        }

        return true;

        // $priority_levels = [];
        // foreach ($requestData->priorities as $priority) {
        //     if (in_array($priority->priority_level, $priority_levels)) {
        //         return false;
        //     }
        //     $this->model->updateOrCreate([
        //         'direct_purchase_id' => $directPurchaseId,
        //         'vendor_id' => $requestData->vendor_id,
        //     ],[
        //         'priority_level' => $requestData->priority_level,
        //     ]);
        // }


    }

    public function deletePriority($requestData, $directPurchaseId)
    {
        $DirectPurchasePriority = $this->model
            ->where('direct_purchase_id', $directPurchaseId)
            ->where('priority_level', $requestData->priority_level)
            ->first();
        if ($DirectPurchasePriority)
            $DirectPurchasePriority->delete();
        return true;
    }

    public function showPriorityByVendor($directPurchaseId,$vendorId)
    {
        return $this->model
            ->where('direct_purchase_id',$directPurchaseId)
            ->where('vendor_id',$vendorId)
            ->first();
    }

    public function deleteVendor($directPurchaseId,$directPurchasePriority)
    {
        $this->model->where('direct_purchase_id', $directPurchaseId)
            ->where('priority_level', '>', $directPurchasePriority->priority_level)
            ->update(['priority_level' => DB::raw('priority_level - 1')]);
        $directPurchasePriority->delete();
        return true;
    }

    public function model(): string
    {
        return DirectPurchasePriority::class;
    }
}
