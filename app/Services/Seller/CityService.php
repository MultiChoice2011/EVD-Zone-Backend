<?php
namespace App\Services\Seller;

use App\Http\Resources\Seller\CityResource;
use App\Repositories\Seller\CityRepository;
use App\Traits\ApiResponseAble;
use Exception;

class CityService
{
    use ApiResponseAble;
    public function __construct(public CityRepository $cityRepository){}
    public function getAllCities(){
        try {
            $cities = $this->cityRepository->getAllCities();
            if (count($cities) > 0)
                return $this->ApiSuccessResponse(CityResource::collection($cities));
            else
                return $this->ApiErrorResponse([],'data not found');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function getCitiesByRegion($regionId)
    {
        try{
            $data = $this->cityRepository->getCitiesByRegion($regionId);
            if($data->count() > 0)
                return $this->ApiSuccessResponse($data);
            return $this->listResponse([]);
        }catch(Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
}
