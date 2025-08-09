<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use App\Models\OptionValue;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use  App\Repositories\Admin\OptionValueTranslationRepository;
class OptionValueRepository extends BaseRepository
{

    private $optionValueTranslationRepository;

    public function __construct(Application $app, OptionValueTranslationRepository $optionValueTranslationRepository)
    {
        parent::__construct($app);

        $this->optionValueTranslationRepository = $optionValueTranslationRepository;

    }


    public function store($data_request,$option_id)
    {
        if (! isset($data_request['option_values']))
            return true;

        foreach ($data_request['option_values'] as $option_value){
            $optionValue = $this->model->create([
                'option_id' => $option_id,
                'key' => $option_value['key'],
            ]);
            if ($optionValue)
                $this->optionValueTranslationRepository->store($option_value, $optionValue->id);
        }

        return $optionValue;
    }

    public function optionValueIds($ids, $optionId)
    {
        return $this->model
            ->whereIn('id', $ids)
            ->where('option_id', $optionId)
            ->pluck('id')
            ->toArray();
    }

    public function destroy($id)
    {
        $optionValue = $this->model->where('option_id', $id)->first();
        if (! $optionValue)
            return false;
        $this->optionValueTranslationRepository->deleteByOptionValueId($optionValue->id);
        return $this->model->where('option_id',$id)->delete();
    }

    public function show($id)
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * OptionValue Model
     *
     * @return string
     */
    public function model(): string
    {
        return OptionValue::class;
    }

}
