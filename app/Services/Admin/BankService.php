<?php
namespace App\Services\Admin;

use App\Http\Resources\Admin\BankResource;
use App\Repositories\Admin\BankRepository;
use App\Traits\ApiResponseAble;
use Exception;

class BankService
{
    use ApiResponseAble;
    public function __construct(public BankRepository $bankRepository){}
    public function index()
    {
        try{
            $banks = $this->bankRepository->getAllBank();
            if($banks->count() > 0)
                return $this->ApiSuccessResponse(BankResource::collection($banks));
            return $this->ApiErrorResponse([],'data not found');
        }catch (Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function store($request)
    {
        try {
            $bank = $this->bankRepository->store($request);
            if ($bank)
                return $this->showResponse(BankResource::make($bank));
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function update($id,$request)
    {
        try{
            $bank = $this->bankRepository->getModelById($id);
            if($bank){
                $this->bankRepository->update($request, $id);
                return $this->showResponse(BankResource::make($bank));
            }
            return $this->ApiErrorResponse([],'data not found');
        }catch(Exception $e){
            return $this->ApiErrorResponse($e->getMessage(), __("admin.general_error"));
        }
    }
    public function destroy($id)
    {
        try{
            $bank = $this->bankRepository->getModelById($id);
            if($bank){
                $bank->delete();
                return $this->ApiSuccessResponse([],'bank delete success');
            }
            return $this->ApiErrorResponse([],'data not found');
        }catch(Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
}
