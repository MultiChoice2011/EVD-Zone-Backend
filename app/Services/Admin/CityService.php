<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\CityRequest;
use App\Repositories\Admin\CityRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Repositories\Admin\RegionRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CityService
{

    use FileUpload, ApiResponseAble;

    private $cityRepository;
    private $languageRepository;
    private $regionRepository;

    public function __construct(CityRepository $cityRepository, LanguageRepository $languageRepository, RegionRepository $regionRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->languageRepository = $languageRepository;
        $this->regionRepository = $regionRepository;
    }

    /**
     *
     * All  Cities.
     *
     */
    public function getAllCities($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $cities = $this->cityRepository->getAllCities($request);
            if (count($cities) > 0)
                return $this->listResponse($cities);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     *
     * All  Cities.
     *
     */
    public function getAllCitiesForm($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $cities = $this->cityRepository->getAllCitiesForm($request);
            if (count($cities) > 0)
                return $this->listResponse($cities);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $city = $this->cityRepository->show($id);
            if (isset($city))
                return $this->showResponse($city);

            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     *
     * Create New City.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCity(CityRequest $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $city = $this->cityRepository->store($data_request);
            if ($city)
                return $this->showResponse($city);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     * Update City.
     *
     * @param integer $city_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCity(CityRequest $request, int $city_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();
        try {
            $city = $this->cityRepository->update($data_request, $city_id);
            if ($city)
                return $this->showResponse($city);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete City.
     *
     * @param int $city_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCity(int $city_id): \Illuminate\Http\JsonResponse
    {
        try {
            $city = $this->cityRepository->show($city_id);
            if ($city) {
                $this->cityRepository->destroy($city_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
