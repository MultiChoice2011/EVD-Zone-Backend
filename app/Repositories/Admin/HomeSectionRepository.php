<?php

namespace App\Repositories\Admin;

use App\Enums\HomeSectionType;
use App\Enums\WebSectionStyle;
use App\Models\HomeSection;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class HomeSectionRepository extends BaseRepository
{

    public function __construct(
        Application $app,
        private HomeSectionTranslationRepository $homeSectionTranslationRepository,
        //private LanguageRepository $languageRepository,
    )
    {
        parent::__construct($app);
    }


    public function index($requestData)
    {
        $displayIn = $requestData->input('display_in', 1);
        return $this->model
            ->where('display_in', $displayIn)
            ->with(['translations','categories:id', 'brands:id', 'brand', 'bannerCategory:id'])
            ->orderBy('order')
            ->get();
    }

    public function storeSection($requestData)
    {
        $maxOrder = $this->model->where('display_in', $requestData->display_in ?? 1)->max('order');
        $section = $this->model->create([
            'order' =>  $maxOrder ? $maxOrder+1 : 1,
            'type' => $requestData->type,
            'display_in' => $requestData->display_in ?? 1,
            'brand_id' => ($requestData->type == HomeSectionType::getTypeBanner() && $requestData->display_in == 2)
                ? $requestData->brand_id
                : null,
            'banner_category_id' => ($requestData->type == HomeSectionType::getTypeBanner() && $requestData->display_in == 2)
                ? $requestData->banner_category_id
                : null,
            'style' => $requestData->type == HomeSectionType::getTypeCategory() ? $requestData->style : null,
        ]);
        if (! $section)
            return false;
        $this->homeSectionTranslationRepository->storeOrUpdate($requestData, $section->id);
        if (isset($requestData->categories) && $requestData->type == HomeSectionType::getTypeCategory())
            $section->categories()->sync($requestData->categories);
        if (isset($requestData->brands) && $requestData->type == HomeSectionType::getTypeCategory())
            $section->brands()->sync($requestData->brands);
        return $section;
    }

    public function show($id)
    {
        return $this->model
            ->where('id', $id)
            ->with([
                'translations',
                'categories:id',
                'brands:id',
                'bannerCategory:id'
            ])
            ->orderBy('order')
            ->first();
    }

    public function updateSection($requestData, $id)
    {
        $section = $this->model->where('id', $id)->first();
        if (! $section)
            return false;
        $section->brand_id = $requestData->brand_id ?? null;
        $section->banner_category_id = $requestData->banner_category_id ?? null;
        $section->style = $requestData->type == HomeSectionType::getTypeCategory() ? $requestData->style : null;
        $section->save();
        $this->homeSectionTranslationRepository->storeOrUpdate($requestData, $section->id);
        $section->categories()->detach();
        $section->brands()->detach();
        if (isset($requestData->categories) && $requestData->type == HomeSectionType::getTypeCategory())
            $section->categories()->sync($requestData->categories);
        if (isset($requestData->brands) && $requestData->type == HomeSectionType::getTypeCategory())
            $section->brands()->sync($requestData->brands);
        return $section;
    }

    public function changeStatus($requestData, $id)
    {
        $section = $this->model->where('id', $id)->first();
        if (! $section)
            return false;
        $section->status = $requestData->status;
        $section->save();
        return $section;
    }

    public function changeOrder($requestData)
    {
        $sections = $this->model->where('display_in', $requestData->display_in ?? 1)->active()->get();
        if (! $sections)
            return false;
        foreach ($sections as $section){
            $newOrder = array_search($section->id, $requestData->orders);
            if ( $newOrder === false )
                $section->order = 0;
            else
                $section->order = $newOrder + 1;
            $section->save();
        }
        return $sections;
    }

    public function deleteSection($id)
    {
        return $this->model->where('id',$id)->delete();
    }


    public function model(): string
    {
        return HomeSection::class;
    }
}
