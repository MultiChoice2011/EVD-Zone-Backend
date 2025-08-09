<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AttributeGroupRequest;
use App\Services\Admin\AttributeGroupService;

class AttributeGroupController extends Controller
{
    public $attributeGroupService;

    /**
     * AttributeGroup  Constructor.
     */
    public function __construct(AttributeGroupService $attributeGroupService)
    {
        $this->attributeGroupService = $attributeGroupService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->attributeGroupService->getAllAttributeGroups($request);
    }

    /**
     *  Store AttributeGroup
     */
    public function store(AttributeGroupRequest $request)
    {
        return $this->attributeGroupService->storeAttributeGroup($request);
    }

    /**
     * show the attributeGroup..
     *
     */
    public function show( $id)
    {
    }



    /**
     * Update the attributeGroup..
     *
     */
    public function update(AttributeGroupRequest $request, int $id)
    {
        return $this->attributeGroupService->updateAttributeGroup($request,$id);
    }
    /**
     *
     * Delete AttributeGroup Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->attributeGroupService->deleteAttributeGroup($id);

    }
    /**
     *
     * Delete Brand Using ID.
     *
     */
    public function destroy_selected(Request $request)
    {
        return $this->attributeGroupService->deleteAttributeGroup($request);

    }

}
