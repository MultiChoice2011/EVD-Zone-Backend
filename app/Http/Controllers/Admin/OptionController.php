<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\OptionRequest;
use App\Services\Admin\OptionService;

class OptionController extends Controller
{
    public $optionService;

    /**
     * Option  Constructor.
     */
    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->optionService->getAllOptions($request);
    }

    /**
     *  Store Option
     */
    public function store(OptionRequest $request)
    {
        return $this->optionService->storeOption($request);
    }

    /**
     * show the option..
     *
     */
    public function show( $id)
    {
        return $this->optionService->show($id);

    }



    /**
     * Update the option..
     *
     */
    public function update(OptionRequest $request, int $id)
    {
        return $this->optionService->updateOption($request,$id);
    }
    /**
     *
     * Delete Option Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->optionService->deleteOption($id);

    }
    /**
     *
     * Delete Brand Using ID.
     *
     */
    public function destroy_selected(Request $request)
    {
        return $this->optionService->deleteOption($request);

    }

}
