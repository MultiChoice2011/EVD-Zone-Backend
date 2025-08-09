<?php
namespace App\Services\Seller;

use App\Http\Resources\Admin\LanguageResource;
use App\Http\Resources\Seller\MainSettingResource;
use App\Repositories\Admin\LanguageRepository;
use App\Repositories\Seller\SettingRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Support\Facades\DB;

class SettingService
{
    use ApiResponseAble;
    public function __construct(public SettingRepository $settingRepository,public LanguageRepository $languageRepository){}
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
}
