<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\IntegrationRepository;
use App\Services\General\OnlineShoppingIntegration\IntegrationServiceFactory;
use App\Traits\ApiResponseAble;
use Illuminate\Http\Request;

class TestIntegrationController extends Controller
{
    use ApiResponseAble;
    public function __construct(
        private IntegrationRepository $integrationRepository,
        private IntegrationServiceFactory $integrationServiceFactory
    ){}
    public function test()
    {
        try {
            $integration = $this->integrationRepository->showByName('like_card');
            $service = $this->integrationServiceFactory::create($integration);
            $requestData = [
                'product_id' => 376,
                'quantity' => 1,
                'original_product_id' => 2,
                'invoice_id' => 1,
            ];
            $testResponse = $service->purchaseProduct($requestData);

            return $this->showResponse($testResponse);
        } catch (\Exception $e) {
            return $this->ApiErrorResponse(null, $e);
        }
    }

}
