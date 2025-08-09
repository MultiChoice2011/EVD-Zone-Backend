<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\CurrencyRequest;
use App\Http\Resources\Front\CurrencyResource;
use App\Repositories\Admin\CurrencyRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;

class CurrencyService
{
    use FileUpload, ApiResponseAble;

    private $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     *
     * All  Currencies.
     *
     */
    public function getAllCurrencies($request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $currencies = $this->currencyRepository->getAllCurrencies($request);
            if (count($currencies) > 0)
                return $this->listResponse($currencies);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $currency = $this->currencyRepository->show($id);
            if (isset($currency))
                return $this->showResponse($currency);

            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function get_currency(): \Illuminate\Http\JsonResponse
    {
        try {
            $currency = $this->currencyRepository->defaultCurrency();
            if (isset($currency))
                return $this->showResponse(new CurrencyResource($currency));

            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     * Create New Currency.
     *
     * @param CurrencyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCurrency(CurrencyRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $currency = $this->currencyRepository->store($request);
            if ($currency)
                return $this->showResponse($currency);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     * Update Currency.
     *
     * @param integer $currency_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidatorException
     */
    public function updateCurrency(CurrencyRequest $request, int $currency_id): \Illuminate\Http\JsonResponse
    {
        try {
            $currency = $this->currencyRepository->updateCurrency($request, $currency_id);
            if ($currency)
                return $this->showResponse($currency);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Currency.
     *
     * @param int $currency_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCurrency(int $currency_id): \Illuminate\Http\JsonResponse
    {
        try {
            $currency = $this->currencyRepository->show($currency_id);
            if ($currency) {
                if ($currency->is_default == 1) {
                    return $this->ApiErrorResponse(null, 'Can not delete default currency.');
                }
                $this->remove_file('currencies', $currency->name);
                $this->currencyRepository->destroy($currency_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }
}
