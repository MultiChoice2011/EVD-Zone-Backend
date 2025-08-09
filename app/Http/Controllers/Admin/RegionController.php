<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RegionRequest;
use App\Services\Admin\RegionService;

class RegionController extends Controller
{
    public $regionService;

    /**
     * Region  Constructor.
     */
    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->regionService->getAllRegions($request);
    }


    /**
     * All Cats
     */
    public function getAllRegionsForm(Request $request)
    {
        return $this->regionService->getAllRegionsForm($request);
    }


    /**
     *  Store Region
     */
    public function store(RegionRequest $request)
    {
        return $this->regionService->storeRegion($request);
    }

    /**
     * show the region..
     *
     */
    public function show( $id)
    {
        return $this->regionService->show($id);
    }


    /**
     * Update the region..
     *
     */
    public function update(RegionRequest $request, int $id)
    {
        return $this->regionService->updateRegion($request,$id);
    }
    /**
     *
     * Delete Region Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->regionService->deleteRegion($id);

    }

}
