<?php

namespace App\Services\Admin;

use App\Repositories\Admin\DirectPurchasePriorityRepository;
use App\Repositories\Admin\DirectPurchaseRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DirectPurchaseService
{
    use ApiResponseAble;

    public function __construct(
        private DirectPurchaseRepository    $directPurchaseRepository,
        private DirectPurchasePriorityRepository    $directPurchasePriorityRepository,
    )
    {}

    public function index($request) : JsonResponse
    {
        try {
            DB::beginTransaction();
            $directPurchases = $this->directPurchaseRepository->index($request);

            DB::commit();
            return $this->ApiSuccessResponse($directPurchases, 'Created Successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function store($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $directPurchase = $this->directPurchaseRepository->store($request);

            if (! $directPurchase)
                return $this->ApiErrorResponse(null, __('admin.general_error'));

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Created Successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            // return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeStatus($request, $directPurchaseId)
    {
        try {
            DB::beginTransaction();
            // change Status for directPurchase
            $directPurchase = $this->directPurchaseRepository->changeStatus($request, $directPurchaseId);
            if (! $directPurchase)
                return $this->ApiErrorResponse(null, 'this id not found');

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Status Changed...!');

        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function deleteVendor($request)
    {
        try{
            $directPurchase = $this->directPurchaseRepository->showByProductId($request->product_id);
            if($directPurchase){
                $directPurchasePriority = $this->directPurchasePriorityRepository->showPriorityByVendor($directPurchase->id,$request->vendor_id);
                if($directPurchasePriority)
                {
                    $this->directPurchasePriorityRepository->deleteVendor($directPurchase->id,$directPurchasePriority);
                    return $this->ApiSuccessResponse([],'delete Success');
                }
                return $this->notFoundResponse();
            }
            return $this->notFoundResponse();
        }catch(\Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(), __('admin.general_error'));
        }
    }


}
