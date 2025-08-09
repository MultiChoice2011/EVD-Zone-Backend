<?php
namespace App\Services\Seller;

use App\Http\Resources\Seller\BankResource;
use App\Repositories\Seller\BankRepository;
use App\Traits\ApiResponseAble;
class BankService
{
    use ApiResponseAble;
    public function __construct(public BankRepository $bankRepository){}
    public function index()
    {
        try{
            $banks = $this->bankRepository->getBankWithCountries();
            if($banks)
                return $this->ApiSuccessResponse(BankResource::collection($banks));
            return $this->ApiErrorResponse([],'data not found');
        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function show($id)
    {
        try{
            $bank = $this->bankRepository->getModeById(intval($id));
            if($bank)
                return $this->ApiSuccessResponse(BankResource::make($bank));
            return $this->ApiErrorResponse([],'data not found');
        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
}
