<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\RegionRequest;
use App\Repositories\Admin\CountryRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Repositories\Admin\RegionRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegionService
{

    use FileUpload, ApiResponseAble;

    private $regionRepository;
    private $languageRepository;
    private $countryRepository;

    public function __construct(RegionRepository $regionRepository, LanguageRepository $languageRepository, CountryRepository $countryRepository)
    {
        $this->regionRepository = $regionRepository;
        $this->languageRepository = $languageRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     *
     * All  Regions.
     *
     */
    public function getAllRegions($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $regions = $this->regionRepository->getAllRegions($request);
            if (count($regions) > 0)
                return $this->listResponse($regions);
            else
                return $this->listResponse([]);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     *
     * All  Regions.
     *
     */
    public function getAllRegionsForm($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $regions = $this->regionRepository->getAllRegionsForm($request);
            if (count($regions) > 0)
                return $this->listResponse($regions);
            else
                return $this->listResponse([]);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     *
     * Create New Region.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeRegion(RegionRequest $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $region = $this->regionRepository->store($data_request);
            if ($region)
                return $this->showResponse($region);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     * Update Region.
     *
     * @param integer $region_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRegion(RegionRequest $request, int $region_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();
        try {
            $region = $this->regionRepository->update($data_request, $region_id);
            if ($region)
                return $this->showResponse($region);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $region = $this->regionRepository->show($id);
            if (isset($region))
                return $this->showResponse($region);

            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Region.
     *
     * @param int $region_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRegion(int $region_id): \Illuminate\Http\JsonResponse
    {
        try {
            $region = $this->regionRepository->show($region_id);
            if ($region) {
                $this->regionRepository->destroy($region_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
