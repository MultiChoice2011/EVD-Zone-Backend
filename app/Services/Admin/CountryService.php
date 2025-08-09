<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\CountryRequest;
use App\Repositories\Admin\CountryRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\Request;

class CountryService
{

    use FileUpload, ApiResponseAble;

    private $countryRepository;
    private $languageRepository;

    public function __construct(CountryRepository $countryRepository, LanguageRepository $languageRepository)
    {
        $this->countryRepository = $countryRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     *
     * All  Countrys.
     *
     */
    public function getAllCountries($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $countries = $this->countryRepository->getAllCountries($request);
            if (count($countries) > 0)
                return $this->listResponse($countries);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     *
     * All  Countrys.
     *
     */
    public function getAllCountriesForm($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $countries = $this->countryRepository->getAllCountriesForm($request);
            if (count($countries) > 0)
                return $this->listResponse($countries);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * create  Countrys.
     */


    /**
     *
     * Create New Country.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCountry(CountryRequest $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('flag');
        if (isset($request->flag))
            $data_request['flag'] = $request->flag;

        try {
            $country = $this->countryRepository->store($data_request);
            if ($country)
                return $this->showResponse($country);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Update Country.
     *
     * @param integer $country_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCountry(CountryRequest $request, int $country_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('flag');
        if (isset($request->flag))
            $data_request['flag'] = $request->flag;

        try {
            $country = $this->countryRepository->update($data_request, $country_id);
            if ($country)
                return $this->showResponse($country);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $country = $this->countryRepository->show($id);
            if ($country)
                return $this->showResponse($country);
            else
                return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Country.
     *
     * @param int $country_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCountry(int $country_id): \Illuminate\Http\JsonResponse
    {
        try {
            $country = $this->countryRepository->show($country_id);
            if ($country) {
                $this->countryRepository->destroy($country_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
