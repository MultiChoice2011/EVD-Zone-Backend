<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorRequest;
use App\Http\Requests\Admin\VendorWallet\Ballance;
use App\Models\Vendor;
use App\Services\Admin\VendorService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public $vendorService;

    /**
     * Vendor  Constructor.
     */
    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->vendorService->getAllVendors($request);
    }


    /**
     *  Store Vendor
     */
    public function store(VendorRequest $request)
    {

        return $this->vendorService->storeVendor($request);
    }

    /**
     * show the vendor
     *
     */
    public function show($id)
    {
        return $this->vendorService->show($id);
    }



    /**
     * Update the vendor
     *
     */
    public function update(VendorRequest $request, int $id)
    {
        return $this->vendorService->updateVendor($request, $id);
    }

    /**
     *
     * Delete Vendor Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->vendorService->deleteVendor($id);

    }
    /**
     *
     * Delete Brand Using ID.
     *
     */
    public function destroy_selected(Request $request)
    {
        return $this->vendorService->destroy_selected($request);

    }
    /**
     *
     * trash sellerGroupLevel
     *
     */
    public function trash()
    {
        return $this->vendorService->trash();

    }

    /**
     *
     * trash sellerGroupLevel
     *
     */
    public function restore(int $id)
    {
        return $this->vendorService->restore($id);

    }
    public function update_status(Request $request, int $id)
    {
        return $this->vendorService->update_status($request, $id);
    }
}
