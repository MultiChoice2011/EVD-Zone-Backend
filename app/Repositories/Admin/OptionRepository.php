<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;
use  App\Repositories\Admin\OptionTranslationRepository;
use  App\Repositories\Admin\OptionValueRepository;
use  App\Repositories\Admin\OptionValueTranslationRepository;
class OptionRepository extends BaseRepository
{

    private $optionTranslationRepository;
    private $optionValueRepository;
    private $optionValueTranslationRepository;

    public function __construct(Application $app, OptionTranslationRepository $optionTranslationRepository, OptionValueRepository $optionValueRepository, OptionValueTranslationRepository $optionValueTranslationRepository)
    {
        parent::__construct($app);

        $this->optionTranslationRepository = $optionTranslationRepository;
        $this->optionValueRepository = $optionValueRepository;
        $this->optionValueTranslationRepository = $optionValueTranslationRepository;

    }
    public function getAllOptions()
    {
        return $this->model->with(['translations','option_values'])->paginate(15);
    }

    public function store($data_request)
    {
        $option = $this->model->create($data_request);
        if ($option)
        {
            $this->optionTranslationRepository->store($data_request['name'], $option->id);
            $this->optionValueRepository->store($data_request, $option->id);
        }

        return $option->load('translations','option_values','option_values.translations');

    }

    public function update($data_request,$option_id)
    {
        $option = $this->model->find($option_id);
        if (! $option)
            return false;
        $option->update($data_request);
        $this->optionTranslationRepository->deleteByOptionId($option->id);
        $this->optionTranslationRepository->store($data_request['name'], $option->id);
        $this->optionValueRepository->destroy($option->id);
        $this->optionValueRepository->store($data_request, $option->id);


        return $option->load('translations','option_values','option_values.translations');

    }

    public function show($id)
    {
        return $this->model->where('id',$id)->with(['translations','option_values','option_values.translations'])->first();
    }
    public function destroy($id)
    {
        try {
            $this->optionTranslationRepository->deleteByOptionId($id);
            $this->optionValueRepository->destroy($id);
        }catch (\Exception $e){
         //
        }
        return $this->model->where('id',$id)->delete();
    }

    /**
     * Option Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Option";
    }

}
