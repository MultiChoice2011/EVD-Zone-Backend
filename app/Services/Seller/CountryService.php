<?php
namespace App\Services\Seller;

use App\Http\Resources\Seller\CountryResource;
use App\Repositories\Seller\CountryRepository;
use App\Traits\ApiResponseAble;
use Exception;

class CountryService
{
    use ApiResponseAble;
    public function __construct(public CountryRepository $countryRepository){}
    public function getAllCountries()
    {
        try {
            $countries = $this->countryRepository->getAllCountries();
            if (count($countries) > 0)
                return $this->ApiSuccessResponse(CountryResource::collection($countries));
            else
                return $this->ApiErrorResponse([],'data not found');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
