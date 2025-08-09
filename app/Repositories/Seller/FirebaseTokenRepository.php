<?php

namespace App\Repositories\Seller;

use App\Models\FirebaseToken;
use App\Models\Seller;
use App\Services\General\NotificationServices\FirebaseService;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class FirebaseTokenRepository extends BaseRepository
{
    public function __construct(
        Application                 $app,
        private FirebaseService     $firebaseService
    )
    {
        parent::__construct($app);
    }

    public function store($requestData, $authSeller)
    {
        $tokenValidate = $this->firebaseService->validateRegistrationTokens($requestData->firebase_token);
        if (count($tokenValidate['valid']) == 0 && count($tokenValidate['unknown']) == 0)
            return false;

        $firebaseToken = $this->model->updateOrCreate([
            'token' => $requestData->firebase_token
        ],[
            'token' => $requestData->firebase_token
        ]);

        if ($authSeller) {
            $authSeller->firebaseTokens()->save($firebaseToken);
            $this->firebaseService->subscribeToTopics($requestData->firebase_token, ['all-sellers', 'seller-'.$authSeller->id]);
        }
        else{
            $firebaseToken->ownerable()->dissociate();
            $firebaseToken->save();
            $this->firebaseService->subscribeToTopics($requestData->firebase_token);
        }

        return $firebaseToken;
    }

    public function makeNullable($requestData, $authSeller)
    {
        $firebaseToken = $this->model
            ->where('token', $requestData->firebase_token)
            ->where('ownerable_type', Seller::class)
            ->where('ownerable_id', $authSeller->id)
            ->first();
        if (! $firebaseToken)
            return false;
        $firebaseToken->ownerable()->dissociate();
        $firebaseToken->save();
        return true;
    }


    public function model(): string
    {
        return FirebaseToken::class;
    }
}
