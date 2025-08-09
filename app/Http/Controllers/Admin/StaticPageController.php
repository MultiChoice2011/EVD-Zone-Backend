<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequests\StaticPageRequest;
use App\Http\Requests\Admin\SettingRequests\StaticPageStatusRequest;
use App\Services\Admin\SettingService;
use App\Services\Admin\StaticPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaticPageController extends Controller
{

    public function __construct(private StaticPageService $staticPageService)
    {}

    public function index()
    {
        return $this->staticPageService->index();
    }

    public function store(StaticPageRequest $request)
    {
        return $this->staticPageService->store($request);
    }

    public function show($pageId)
    {
        return $this->staticPageService->show($pageId);
    }

    public function update(StaticPageRequest $request, int $id)
    {
        return $this->staticPageService->update($request, $id);
    }

    public function changeStatus(StaticPageStatusRequest $request, int $id)
    {
        return $this->staticPageService->changeStatus($request, $id);
    }

    public function delete(int $id)
    {
        return $this->staticPageService->delete($id);
    }



}
