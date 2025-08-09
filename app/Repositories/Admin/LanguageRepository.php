<?php

namespace App\Repositories\Admin;

use App\Enums\GeneralStatusEnum;
use Prettus\Repository\Eloquent\BaseRepository;

class LanguageRepository extends BaseRepository
{


    public function getAllLanguages(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }


    public function store($data_request)
    {
        return $this->model->create($data_request);
    }

    public function show($language_id)
    {
        return $this->model->find($language_id);
    }

    public function activeLanguage($id)
    {
        return $this->model->where('id', $id)->active()->first();
    }

    public function getLangByCode($code)
    {
        return $this->model->where('code',$code)->first();
    }

    public function update($data_request, $language_id)
    {
        $language = $this->model->find($language_id);
        $language->update($data_request);
        return $language;
    }

    public function makeAllLanguagesAvailable()
    {
        return $this->model->query()->update(['status' => GeneralStatusEnum::getStatusActive()]);
    }

    public function unavailableLanguages(array $langsIds, array $defaultLanguages)
    {
        foreach ($langsIds as $langId){
            $language = $this->model
                ->where('id', $langId)
                ->whereNotIn('id', $defaultLanguages)
                ->first();
            if (! $language)
                continue;
            $language->status = GeneralStatusEnum::getStatusInactive();
            $language->save();
        }
        return true;
    }

    public function destroy($language_id)
    {
        return $this->model->find($language_id)->delete();
    }

    /**
     * Language Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Language";
    }
}
