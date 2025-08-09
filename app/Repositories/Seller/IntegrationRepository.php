<?php

namespace App\Repositories\Seller;

use App\Models\Integration;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class IntegrationRepository extends BaseRepository
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function showByName($name)
    {
        $integration = $this->model
            ->where('name', $name)
            ->with(['keys'])
            ->first();
        if (!$integration) {
            return null; // or throw an exception if needed
        }
        return $this->formatKeys($integration);
    }

    public function showById($id)
    {
        $integration = $this->model
            ->where('id', $id)
            ->with(['keys'])
            ->first();

        if (!$integration) {
            return null; // or throw an exception if needed
        }
        return $this->formatKeys($integration);
    }

    //////////////////////////////////////////////
    //////////////////// Assets //////////////////
    //////////////////////////////////////////////

    public function formatKeys($integration)
    {
        $formattedKeys = [];
        foreach ($integration->keys as $key) {
            $formattedKeys[$key->key] = $key->value;
        }
        unset($integration['keys']);
        $integration['keys'] = $formattedKeys;
        return $integration;
    }

    public function hashKeys($integration)
    {
        $hashed = [];
        foreach ($integration->keys as $integrationKey => $integrationValue) {
            if ($integrationValue && strlen($integrationValue) > 3)
                $hashed[$integrationKey] = str_repeat('*',
                        strlen($integrationValue) - 3) . mb_substr($integrationValue, -3,null, "utf-8");
            else
                $hashed[$integrationKey] = str_repeat('*', strlen($integrationValue));
        }
        unset($integration['keys']);
        $integration['keys'] = $hashed;
        return $integration;
    }


    ///

    public function model(): string
    {
        return Integration::class;
    }
}
