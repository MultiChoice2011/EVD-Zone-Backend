<?php
namespace App\Services\Seller;

use App\Http\Resources\Seller\CurrencyResource;
use App\Repositories\Seller\CurrencyRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CurrencyService
{
    use ApiResponseAble;
    public function __construct(private CurrencyRepository $currencyRepository){}
    public function index()
    {
        try{
            $currencies = $this->currencyRepository->getAllCurrencies();
            if(count($currencies)>0)
                return $this->ApiSuccessResponse(CurrencyResource::collection($currencies));
            return $this->listResponse([]);
        }catch (Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function storeDefault($request)
    {
        try {
            DB::beginTransaction();
            $authSeller = Auth::guard('sellerApi')->user();
            $currency = $this->currencyRepository->storeDefault($request, $authSeller);
            if (! $currency)
                return $this->ApiErrorResponse(null, __('admin.general_error'));

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Updated successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
