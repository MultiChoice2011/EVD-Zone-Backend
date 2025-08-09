<?php

namespace App\Repositories\Admin;

use App\Helpers\FileUpload;
use App\Models\Language;
use App\Models\Setting;
use App\Models\StaticPage;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class StaticPageRepository extends BaseRepository
{
    use FileUpload;

    public function __construct(
        Application $app,
        private StaticPageTranslationRepository $staticPageTranslationRepository,
    )
    {
        parent::__construct($app);
    }

    public function index()
    {
        return $this->model->paginate(PAGINATION_COUNT_ADMIN);
    }

    public function storePage($requestData)
    {
        $page = $this->model->create([
            'web' => $requestData->web ?? 0,
            'mobile' => $requestData->mobile ?? 0,
        ]);
        if ($page) {
            $this->staticPageTranslationRepository->store($page, $requestData);
        }
        return $page;
    }

    public function show($id)
    {
        return $this->model->with('translations')->where('id', $id)->first();
    }

    public function updatePage($requestData, $id)
    {
        $page = $this->model->where('id', $id)->where('key', null)->first();
        if (! $page)
            return false;
        $page->web = $requestData->web;
        $page->mobile = $requestData->mobile;
        $page->save();
        $this->staticPageTranslationRepository->deleteByStaticPageId($id);
        if ($page) {
            $this->staticPageTranslationRepository->store($page, $requestData);
        }
        return $page;
    }

    public function changeStatus($requestData, $id)
    {
        $page = $this->model->where('id', $id)->where('key', null)->first();
        if(!$page){
            return false;
        }
        // change status
        $page->status = $requestData->status;
        $page->save();

        return $page;
    }


    public function deletePage($id)
    {
        return $this->model->where('id',$id)->where('key', null)->delete();
    }


    public function model(): string
    {
        return StaticPage::class;
    }
}
