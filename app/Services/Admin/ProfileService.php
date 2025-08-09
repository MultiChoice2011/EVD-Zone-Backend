<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    use ApiResponseAble;

    public function __construct(
        private AdminRepository             $adminRepository,
    )
    {}

    public function index($request)
    {
        try {
            DB::beginTransaction();
            $authAdmin = Auth::guard('adminApi')->user();
            $data = $authAdmin->load('roles','permissions');

            DB::commit();
            return $this->ApiSuccessResponse($data, 'Profile Data...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }




}
