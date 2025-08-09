<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\SellerGroupRequest;
use App\Services\Admin\SellerGroupService;

class SellerGroupController extends Controller
{
    public $sellerGroupService;

    /**
     * sellerGroup  Constructor.
     */
    public function __construct(sellerGroupService $sellerGroupService)
    {
        $this->sellerGroupService = $sellerGroupService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->sellerGroupService->getAllsellerGroups($request);
    }


    /**
     *  Store sellerGroup
     */
    public function store(sellerGroupRequest $request)
    {
        return $this->sellerGroupService->storeSellerGroup($request);
    }

    /**
     * show the sellerGroup..
     *
     */
    public function show($id)
    {
        return $this->sellerGroupService->show($id);
    }


    /**
     * Update the sellerGroup..
     *
     */
    public function update(sellerGroupRequest $request, int $id)
    {
        return $this->sellerGroupService->updateSellerGroup($request, $id);
    }

    /**
     *
     * Delete sellerGroup Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->sellerGroupService->deleteSellerGroup($id);

    }

    /**
     *
     * trash sellerGroup
     *
     */
    public function trash()
    {
        return $this->sellerGroupService->trash();

    }

    /**
     *
     * trash sellerGroup
     *
     */
    public function restore(int $id)
    {
        return $this->sellerGroupService->restore($id);

    }

    public function update_status(Request $request, int $id)
    {
        return $this->sellerGroupService->update_status($request, $id);
    }
    public function auto_assign(Request $request, int $id)
    {
        return $this->sellerGroupService->auto_assign($request, $id);
    }

    public function destroy_selected(Request $request)
    {
        return $this->sellerGroupService->destroy_selected($request);

    }

}
