<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\IntegrationResource;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Admin\VendorRepository;
use App\Services\General\SmsVerification\MsegatService;
use App\Traits\ApiResponseAble;
use ReflectionClass;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;

class IntegrationService
{
    use ApiResponseAble;

    public function __construct(
        protected Container $container,
        protected IntegrationRepository $integrationRepository,
        protected VendorRepository $vendorRepository,
    )
    {}

    public function index()
    {
        try {
            DB::beginTransaction();
            // get all Api Integrations
            $integrations = $this->integrationRepository->index();

            foreach ($integrations as $integration){
                $integration = $this->integrationRepository->formatKeys($integration);
                $reflector = new ReflectionClass($integration->model);
                $service = $reflector->newInstanceArgs([$integration]);
                //$service = $this->container->make($integration->model, ['integration' => $integration]);
                if (! method_exists($service, 'checkBalance')){
                    $integration->balance = null;
                }else{
                    $balance = $service->checkBalance();
                    $integration->balance = $balance ?: null;
                }

                $integration['keys'] = null;
                // $integration = $this->integrationRepository->hashKeys($integration);
            }

            DB::commit();
            return $this->showResponse(IntegrationResource::collection($integrations)->resource);
            //return $this->showResponse($integrations);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateIntegration($request, $integrationId)
    {
        try {
            DB::beginTransaction();
            // update Integration
            $integration = $this->integrationRepository->updateIntegration($request, $integrationId);
            if (! $integration)
                return $this->ApiErrorResponse(null, 'this id not found');

            // update vendor row
            if ($request->vendor_id){
                $vendor = $this->vendorRepository->setIntegrationId($integration->id, $request->vendor_id);
                if (! $vendor)
                    return $this->ApiErrorResponse(null, 'vendor not found');
            }

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Updated Successfully...!');

        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeStatus($request, $integrationId)
    {
        try {
            DB::beginTransaction();
            // change Status for Integration
            $integration = $this->integrationRepository->changeStatus($request, $integrationId);
            if (! $integration)
                return $this->ApiErrorResponse(null, 'this id not found');

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Status Changed...!');

        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }





}
