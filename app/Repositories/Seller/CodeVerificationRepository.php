<?php

namespace App\Repositories\Seller;

use App\Models\CodeVerification;
use Carbon\Carbon;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class CodeVerificationRepository extends BaseRepository
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function store($requestData)
    {
        return $this->model->updateOrCreate(
            [
                'verifiable_type' => $requestData['verifiable_type'],
                'verifiable_id' => $requestData['verifiable_id'],
                'type' => $requestData['type'],
            ],
            [
                'verifiable_type' => $requestData['verifiable_type'],
                'verifiable_id' => $requestData['verifiable_id'],
                'code' => $requestData['code'],
                'type' => $requestData['type'],
                'token' => $requestData['token'] ?? null,
                'expire_at' => $requestData['expire_at'],
                'is_id' => $requestData['is_id'],
                'used' => 0,
            ]
        );
    }

    public function getByCustomerId($requestData)
    {
        return $this->model
            ->where('verifiable_type', $requestData->verifiable_type)
            ->where('verifiable_id', $requestData->verifiable_id)
            ->where('expire_at', '>=', Carbon::now())
            ->where('used', 0)
            ->first();
    }

    public function getByResetToken($requestData)
    {
        return $this->model
            ->where('verifiable_type', $requestData->verifiable_type)
            ->where('verifiable_id', $requestData->verifiable_id)
            ->where('expire_at', '>=', Carbon::now())
            ->where('token', $requestData->reset_token)
            ->where('used', 1)
            ->first();
    }

    public function updateUsed(CodeVerification $verification)
    {
        $verification->used = 1;
        $verification->save();
        return true;
    }

    public function updateToken(CodeVerification $verification)
    {
        $verification->token = Str::random(40);
        $verification->expire_at = Carbon::now()->addMinutes(5);
        $verification->save();
        return $verification;
    }


    /**
     * CodeVerification Model
     *
     * @return string
     */
    public function model(): string
    {
        return CodeVerification::class;
    }
}
