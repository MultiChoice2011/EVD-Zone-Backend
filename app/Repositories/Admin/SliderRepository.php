<?php

namespace App\Repositories\Admin;

use App\Models\Slider;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class SliderRepository extends BaseRepository
{

    public function __construct(
        Application $app,
        private SliderTranslationRepository $sliderTranslationRepository,
    )
    {
        parent::__construct($app);
    }


    public function index($requestData)
    {
        $displayIn = $requestData->input('display_in', 1);
        return $this->model
            ->where('display_in', $displayIn)
            ->with('translations')
            ->orderBy('order')
            ->get();
    }

    public function storeSlider($requestData)
    {
        $maxOrder = $this->model->where('display_in', $requestData->display_in ?? 1)->max('order');
        $slider = $this->model->create([
            'order' =>  $maxOrder ? $maxOrder+1 : 1,
            'display_in' =>  $requestData->display_in ?? 1,
            'brand_id' =>  $requestData->brand_id ?? null,
            'status' => $requestData->status,
        ]);
        if (! $slider)
            return false;
        $this->sliderTranslationRepository->storeOrUpdate($requestData, $slider->id);
        return $slider;
    }

    public function show($id)
    {
        return $this->model
            ->where('id', $id)
            ->with('translations')
            ->orderBy('order')
            ->first();
    }

    public function updateSlider($requestData, $id)
    {
        $slider = $this->model->where('id', $id)->first();
        if (! $slider)
            return false;
        $slider->brand_id = $requestData->brand_id ?? null;
        $slider->save();
        $this->sliderTranslationRepository->storeOrUpdate($requestData, $slider->id);
        return $slider;
    }

    public function changeStatus($requestData, $id)
    {
        $slider = $this->model->where('id', $id)->first();
        if (! $slider)
            return false;
        $slider->status = $requestData->status;
        $slider->save();
        return $slider;
    }

    public function changeOrder($requestData)
    {
        $sliders = $this->model->where('display_in', $requestData->display_in ?? 1)->active()->get();
        if (! $sliders)
            return false;
        foreach ($sliders as $slider){
            $newOrder = array_search($slider->id, $requestData->orders);
            if ( $newOrder === false )
                $slider->order = 0;
            else
                $slider->order = $newOrder + 1;
            $slider->save();
        }
        return $sliders;
    }

    public function deleteSlider($id)
    {
        return $this->model->where('id',$id)->delete();
    }


    public function model(): string
    {
        return Slider::class;
    }
}
