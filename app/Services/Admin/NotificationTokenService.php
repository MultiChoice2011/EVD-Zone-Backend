<?php

namespace App\Services\Admin;

use App\Repositories\Admin\FirebaseTokenRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationTokenService
{
    use ApiResponseAble;

    public function __construct(
        private FirebaseTokenRepository $firebaseTokenRepository
    )
    {}


    public function firebaseStore($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get authed customer
            $authAdmin = Auth::guard('adminApi')->user();
            if (! $authAdmin) {
                return $this->ApiErrorResponse(null, __('admin.general_error'));
            }
            $firebaseToken = $this->firebaseTokenRepository->store($request, $authAdmin);
            if (! $firebaseToken)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($firebaseToken);
        } catch (Exception $e) {
            DB::rollBack();
            // return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

}
