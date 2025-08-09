<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use  App\Repositories\Admin\RegionTranslationRepository;
class RegionRepository extends BaseRepository
{

    private $regionTranslationRepository;

    public function __construct(Application $app, RegionTranslationRepository $regionTranslationRepository)
    {
        parent::__construct($app);

        $this->regionTranslationRepository = $regionTranslationRepository;

    }

    public function getAllRegions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with('translations')->get();
    }

    public function getAllRegionsForm(): \Illuminate\Database\Eloquent\Collection
    {
        $lang = Language::where('code', app()->getLocale())->first();
        return $this->model->leftJoin('region_translations', function ($join) {
            $join->on('regions.id', '=', 'region_translations.region_id');
        })->select('regions.*')->addSelect('region_translations.name')
            ->where('region_translations.language_id', $lang->id)
            ->get();
    }


    public function store($data_request)
    {
        $region = $this->model->create($data_request);
        if ($region)
            $this->regionTranslationRepository->store($data_request['name'], $region->id);

        return $region;

    }

    public function update($data_request,$region_id)
    {
        $region = $this->model->find($region_id);
        $region->update($data_request);
        $regionTranslation = $this->regionTranslationRepository->deleteByRegionId($region->id);
        if ($regionTranslation)
            $this->regionTranslationRepository->store($data_request['name'], $region->id);

        return $region;

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
     * Region Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Region";
    }
}
