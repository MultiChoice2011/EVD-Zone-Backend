<?php
namespace App\Services\Seller;

use App\Repositories\Seller\RegionRepository;
use App\Traits\ApiResponseAble;
use Exception;

class RegionService
{
    use ApiResponseAble;
    public function __construct(public RegionRepository $regionRepository){}
    public function index($request)
    {
        try{
            $data = $this->regionRepository->getRegions($request);
            if($data->count() > 0)
                return $this->ApiSuccessResponse($data);
            return $this->listResponse([]);
        }catch(Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function getRegionsByCountry($countryId)
    {
        try{
            $data = $this->regionRepository->getRegionsByCountry($countryId);
            if($data->count() > 0)
                return $this->ApiSuccessResponse($data);
            return $this->listResponse([]);
        }catch(Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
}
