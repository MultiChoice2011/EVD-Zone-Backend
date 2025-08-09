<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\LanguageResource;
use App\Http\Resources\Admin\MainSettingResource;
use App\Repositories\Admin\LanguageRepository;
use App\Repositories\Admin\SettingRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;

class SettingService
{
    use ApiResponseAble;

    public function __construct(
        private SettingRepository $settingRepository,
        private LanguageRepository $languageRepository
    )
    {}

    public function mainSettings()
    {
        try {
            DB::beginTransaction();
            $settings = $this->settingRepository->mainSettings();
            $languages = $this->languageRepository->get();
            $data = [
                'settings' => MainSettingResource::collection($settings),
                'languages' => LanguageResource::collection($languages),
            ];
            DB::commit();
            return $this->ApiSuccessResponse($data, 'Main Settings...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateMainSettings($request)
    {
        try {
            DB::beginTransaction();
            $defaultLanguages = $this->settingRepository->updateMainSettings($request);
            if (! $defaultLanguages)
                return $this->ApiErrorResponse();
            // Make languages unavailable
            if (empty($request->unavailable_languages))
                $languageStatus = $this->languageRepository->makeAllLanguagesAvailable();
            else
                $languageStatus = $this->languageRepository->unavailableLanguages($request->unavailable_languages, $defaultLanguages);

            if (! $languageStatus)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Updated Successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


}
