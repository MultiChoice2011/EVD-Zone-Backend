<?php
namespace App\Services\Admin;

use App\Http\Resources\Admin\BankCommissionResource;
use App\Repositories\Admin\BankCommissionRepository;
use App\Repositories\Admin\BankCommissionSettingRepository;
use App\Traits\ApiResponseAble;

class BankCommissionService
{
    use ApiResponseAble;
    public function __construct(
        public BankCommissionRepository $bankCommissionRepository,
        public BankCommissionSettingRepository $bankCommissionSettingRepository

    ){}
    public function index()
    {
        try{

            $data = $this->bankCommissionRepository->getBankCommissions();
            if($data->count() > 0){
                return $this->ApiSuccessResponse(BankCommissionResource::collection($data));
            }
            return $this->listResponse([]);
        }catch (\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function setSetting($request)
    {
        try{
            $data = $this->bankCommissionSettingRepository->store($request);
            if($data){
                return $this->ApiSuccessResponse([],'bank commission setting added successfully');
            }
            return $this->ApiErrorResponse([], trans('admin.general_error'));
        }catch (\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
}
