<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\SellerGroupLevelRequest;
use App\Services\Admin\SellerGroupLevelService;

class SellerGroupLevelController extends Controller
{
    public $sellerGroupLevelService;

    /**
     * sellerGroupLevel  Constructor.
     */
    public function __construct(SellerGroupLevelService $sellerGroupLevelService)
    {
        $this->sellerGroupLevelService = $sellerGroupLevelService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->sellerGroupLevelService->getAllsellerGroupLevels($request);
    }


    /**
     *  Store sellerGroupLevel
     */
    public function store(SellerGroupLevelRequest $request)
    {

        return $this->sellerGroupLevelService->storeSellerGroupLevel($request);
    }

    /**
     * show the sellerGroupLevel..
     *
     */
    public function show($id)
    {
        return $this->sellerGroupLevelService->show($id);
    }


    /**
     * Update the sellerGroupLevel..
     *
     */
    public function update(SellerGroupLevelRequest $request, int $id)
    {
        return $this->sellerGroupLevelService->updateSellerGroupLevel($request, $id);
    }

    /**
     *
     * Delete sellerGroupLevel Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->sellerGroupLevelService->deleteSellerGroupLevel($id);

    }

    /**
     *
     * trash sellerGroupLevel
     *
     */
    public function trash()
    {
        return $this->sellerGroupLevelService->trash();

    }

    /**
     *
     * trash sellerGroupLevel
     *
     */
    public function restore(int $id)
    {
        return $this->sellerGroupLevelService->restore($id);

    }

    public function update_status(Request $request, int $id)
    {
        return $this->sellerGroupLevelService->update_status($request, $id);
    }

}
