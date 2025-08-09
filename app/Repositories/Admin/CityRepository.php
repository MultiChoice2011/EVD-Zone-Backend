<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use  App\Repositories\Admin\CityTranslationRepository;
class CityRepository extends BaseRepository
{

    private $cityTranslationRepository;

    public function __construct(Application $app, CityTranslationRepository $cityTranslationRepository)
    {
        parent::__construct($app);

        $this->cityTranslationRepository = $cityTranslationRepository;

    }

    public function getAllCities(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    public function getAllCitiesForm(): \Illuminate\Database\Eloquent\Collection
    {
        $lang = Language::where('code', app()->getLocale())->first();
        return $this->model->leftJoin('city_translations', function ($join) {
            $join->on('cities.id', '=', 'city_translations.city_id');
        })->select('cities.*')->addSelect('city_translations.name')
            ->where('city_translations.language_id', $lang->id)
            ->get();
    }
    public function store($data_request)
    {
        $city = $this->model->create($data_request);
        if ($city)
            $this->cityTranslationRepository->store($data_request['name'], $city->id);

        return $city;

    }

    public function update($data_request,$city_id)
    {
        $city = $this->model->find($city_id);
        $city->update($data_request);
        $cityTranslation = $this->cityTranslationRepository->deleteByCityId($city->id);
        if ($cityTranslation)
            $this->cityTranslationRepository->store($data_request['name'], $city->id);

        return $city;

    }

    public function show($id)
    {
        return $this->model->where('id',$id)->with('translations')->first();
    }
    public function destroy($id)
    {
        return $this->model->where('id',$id)->delete();
    }

    /**
     * City Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\City";
    }
}
