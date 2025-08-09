<?php
namespace App\Services\Seller\Helpers;

use App\Helpers\FileUpload;
use App\Models\Currency;
use App\Repositories\Seller\CountryRepository;
use App\Services\General\CurrencyService;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SellerHelperService
{
    use ApiResponseAble,FileUpload;
    public function __construct(
        private CountryRepository                   $countryRepository,
    ){}

    public function getDefaultSellerCurrency(int $countryId): Currency
    {
        // get country model based on country id
        $country = $this->countryRepository->showCurrencyRelatedById($countryId);

        // check if country exist
        if ($country && $country->currency) {
            $currency = $country->currency;
        }else{
            $currency = CurrencyService::getCurrentCurrency();
        }

        return $currency;
    }
}
