<?php

namespace App\Repositories\Admin;

use App\Helpers\FileUpload;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class VendorRepository extends BaseRepository
{
    use FileUpload;

    public function __construct(
        Application $app,
        private VendorAttachmentRepository $vendorAttachmentRepository,
    )
    {
        parent::__construct($app);
    }

    public function getAllVendors($requestData)
    {
        if(in_array($requestData->input('sort_by'), ['created_at', 'name', 'owner_name', 'phone', 'status']))
            $sortBy = $requestData->input('sort_by');
        else
            $sortBy = 'created_at';
        $sortDirection = $requestData->input('sort_direction', 'desc');
        $searchTerm = $requestData->input('search', '');
        $query = $this->model->query();
        $query->select('id', 'name', 'owner_name', 'logo', 'phone', 'status');
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);
        return $query->paginate(PAGINATION_COUNT_ADMIN);
        //return $query->latest()->with(['country','region', 'city'])->paginate(PAGINATION_COUNT_ADMIN);
    }

    public function store($requestData)
    {
        $vendor = $this->model->create([
            'name' => $requestData->name,
            'email' => $requestData->email,
            'status' => $requestData->status,
            'is_service' => $requestData->is_service ?? 0,
            'serial_number' => $requestData->serial_number ?? null,
            'owner_name' => $requestData->owner_name ?? null,
            'logo' => $requestData->logo_url ?? 'images/no-logo.png',
            'country_id' => $requestData->country_id,
            'street' => $requestData->street ?? null,
            'phone' => $requestData->phone,
            'meta_keywords' => $requestData->meta_keywords,
            'web' => $requestData->web ?? 1,
            'mobile' => $requestData->mobile ?? 1,
            'city_id' => $requestData->city_id,
            'region_id' => $requestData->region_id,
        ]);
        if ($vendor)
            $this->vendorAttachmentRepository->store($requestData, $vendor->id);

        return $vendor;
    }

    public function updateVendor($requestData, $vendor_id)
    {
        $vendor = $this->model->where('id', $vendor_id)->first();
        // if (!$vendor || ($vendor->name == 'special-service-vendor' && $vendor->owner_name == 'special-service-vendor' && $vendor->email == 'MC@mail.com'))
        //     return false;
        if (isset($requestData->logo)){
            // save new image
            $vendor->logo = $requestData->logo;
            $vendor->save();
        }
        $vendor->update([
            'name' => $requestData->name,
            'email' => $requestData->email,
            'status' => $requestData->status,
            'is_service' => $requestData->is_service ?? 0,
            'serial_number' => $requestData->serial_number ?? null,
            'owner_name' => $requestData->owner_name ?? null,
            'country_id' => $requestData->country_id,
            'street' => $requestData->street ?? null,
            'phone' => $requestData->phone,
            'meta_keywords' => $requestData->meta_keywords,
            'web' => $requestData->web ?? 1,
            'mobile' => $requestData->mobile ?? 1,
            'city_id' => $requestData->city_id,
            'region_id' => $requestData->region_id,
        ]);
        if ($requestData->hasFile('attachments')){
            $this->vendorAttachmentRepository->deleteByVendorId($vendor->id);
            $this->vendorAttachmentRepository->store($requestData, $vendor->id);
        }
        return true;
    }

    public function show($id)
    {
        return $this->model->with(['country','region', 'city','attachments'])->find($id);
    }

    public function showWithVendorProducts($id)
    {
        return $this->model->with([
            'country',
            'region',
            'city',
            'VendorProducts.product:id,brand_id',
            'VendorProducts.brand:id,status'
        ])->find($id);
    }

    public function setIntegrationId($integrationId, $id)
    {
        $vendor = $this->model->find($id);
        if (! $vendor)
            return false;
        $vendor->integration_id = $integrationId;
        $vendor->save();
        return $vendor;
    }

    public function destroy($id)
    {
        $vendor = $this->model->where('id', $id)->first();
        if (
            // !$vendor ||
            // ($vendor->name == 'special-service-vendor' && $vendor->owner_name == 'special-service-vendor' && $vendor->email == 'MC@mail.com') ||
            $vendor->integration()->count() > 0 ||
            $vendor->VendorProducts()->count() > 0
        ){
            return false;
        }
        return $vendor->delete();
    }

    public function update_status($data_request, $vendor_id)
    {
        $vendor = $this->model->find($vendor_id);
        if (!$vendor || ($vendor->name == 'special-service-vendor' && $vendor->owner_name == 'special-service-vendor' && $vendor->email == 'MC@mail.com'))
            return false;
        $vendor->update($data_request);
        return $vendor;

    }

    public function destroy_selected($ids)
    {

        foreach ($ids as $id) {
            $vendor = $this->model->findOrFail($id);
            if ($vendor)
                $vendor->delete();
        }
        return true;
    }

    public function trash()
    {
        return $this->model->onlyTrashed()->get();
    }

    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)->restore();

    }

    /**
     * Vendor Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Vendor";
    }
}
