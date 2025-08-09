<?php

namespace App\Services\Admin;

use App\Repositories\Admin\SettingRepository;
use App\Repositories\Admin\ValueAddedTaxRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValueAddedTaxService
{
    use ApiResponseAble;

    public function __construct(
        private ValueAddedTaxRepository $valueAddedTaxRepository,
        private SettingRepository $settingRepository,
    )
    {}

    public function index()
    {
        try {
            DB::beginTransaction();
            $data = [];
            $data['setting_taxes'] = $this->settingRepository->getTaxesKeys();
            $data['taxes'] = $this->valueAddedTaxRepository->index();
            DB::commit();
            return $this->ApiSuccessResponse($data, 'Taxes...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            $tax = $this->valueAddedTaxRepository->storeTax($request);
            if (! $tax)
                return $this->ApiErrorResponse();
            DB::commit();
            return $this->ApiSuccessResponse(null, 'Created Successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();
            $tax = $this->valueAddedTaxRepository->updateTax($request, $id);
            if (! $tax)
                return $this->ApiErrorResponse(null, 'May be id not found');

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Updated Successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeStatus($request, int $id): JsonResponse
    {
        try {
            $tax = $this->valueAddedTaxRepository->changeStatus($request, $id);
            if (! $tax)
                return $this->ApiErrorResponse(null, 'May be id not found');

            return $this->ApiSuccessResponse(null, "Status Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $taxDeleted = $this->valueAddedTaxRepository->deleteTax($id);
            if (! $taxDeleted)
                return $this->ApiErrorResponse(null, 'May be id not found');

            return $this->ApiSuccessResponse(null, "Deleted Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updatePricesDisplay($request): JsonResponse
    {
        try {
            $tax = $this->settingRepository->updatePricesDisplay($request);
            if (! $tax)
                return $this->ApiErrorResponse();

            return $this->ApiSuccessResponse(null, "Updated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateTaxNumber($request): JsonResponse
    {
        try {
            $tax = $this->settingRepository->updateTaxNumber($request);
            if (! $tax)
                return $this->ApiErrorResponse();

            return $this->ApiSuccessResponse(null, "Updated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }




}
