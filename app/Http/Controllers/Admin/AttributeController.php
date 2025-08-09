<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeTranslation;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AttributeRequest;
use App\Services\Admin\AttributeService;

class AttributeController extends Controller
{
    public $attributeService;

    /**
     * Attribute  Constructor.
     */
    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->attributeService->getAllAttributes($request);
    }


    /**
     *  Store Attribute
     */
    public function store(AttributeRequest $request)
    {
        return $this->attributeService->storeAttribute($request);
    }

    /**
     * show the attribute..
     *
     */
    public function show( $id)
    {
    }

    /**
     * Update the attribute..
     *
     */
    public function update(AttributeRequest $request, int $id)
    {
        return $this->attributeService->updateAttribute($request,$id);
    }
    /**
     *
     * Delete Attribute Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->attributeService->deleteAttribute($id);

    }
    public function autocomplete(Request $request) {
        $lang = Language::where('code', app()->getLocale())->first();
        $json = array();
        if (isset($request->filter_name)) {
            $results = AttributeTranslation::where('language_id',$lang->id)->where('name', 'LIKE','%'.$request->filter_name.'%')->get();
            foreach ($results as $result) {
                $json[] = array(
                    'attribute_id'    => $result['attribute_id'],
                    'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                );
            }
        }
        return response()->json($json);
    }
    /**
     *
     * Delete Brand Using ID.
     *
     */
    public function destroy_selected(Request $request)
    {
        return $this->attributeService->deleteAttribute($request);

    }
}
