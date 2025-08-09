<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class CountryRepository extends BaseRepository
{

    private $countryTranslationRepository;

    public function __construct(Application $app, CountryTranslationRepository $countryTranslationRepository)
    {
        parent::__construct($app);

        $this->countryTranslationRepository = $countryTranslationRepository;

    }

    public function getAllCountries(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with('translations')->get();
    }

    public function getAllCountriesForm(): \Illuminate\Database\Eloquent\Collection
    {
        $lang = Language::where('code', app()->getLocale())->first();
        return $this->model->leftJoin('country_translations', function ($join) {
            $join->on('countries.id', '=', 'country_translations.country_id');
        })->select('countries.*')->addSelect('country_translations.name')
            ->where('country_translations.language_id', $lang->id)
            ->get();
    }

    public function store($data_request)
    {
        $country = $this->model->create($data_request);
        if ($country)
            $this->countryTranslationRepository->store($data_request['name'], $country->id);

        return $country;

    }

    public function update($data_request, $country_id)
    {
        $country = $this->model->find($country_id);
        $country->update($data_request);
        $countryTranslation = $this->countryTranslationRepository->deleteByCountryId($country->id);
        if ($countryTranslation)
            $this->countryTranslationRepository->store($data_request['name'], $country->id);

        return $country;

    }


    public function show($id)
    {
        return $this->model->where('id', $id)->with('translations')->first();
    }

    public function destroy($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    /**
     * Country Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Country";
    }
}
