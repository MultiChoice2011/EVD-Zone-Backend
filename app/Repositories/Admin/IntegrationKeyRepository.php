<?php

namespace App\Repositories\Admin;

use App\Models\IntegrationKey;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class IntegrationKeyRepository extends BaseRepository
{

    public function updateKeys($requestData, $id)
    {

        $keys = collect($requestData->keys)->pluck('key')->toArray();

        $integrationKeys = $this->model
            ->where('integration_id', $id)
            ->whereIn('key', $keys)
            ->get();

        // Group the integration keys by their key for easier access
        $integrationKeysByKey = $integrationKeys->keyBy('key');
        Log::info($integrationKeysByKey);

        // Loop through the request keys
        foreach ($requestData->keys as $item) {
            // Check if the integration key exists
            if (isset($integrationKeysByKey[$item['key']])) {
                // Update the value of the integration key
                $integrationKeysByKey[$item['key']]->update(['value' => $item['value']]);
            }
        }


//        // check all keys in request if exist in integration_keys table by parent table id
//        foreach ($requestData->keys as $item){
//            $integrationKey = $this->model
//                ->where('integration_id', $id)
//                ->where('key', $item->key)
//                ->first();
//            if ($integrationKey) {
//                // update and save its values
//                $integrationKey->value = $item->value;
//                $integrationKey->save();
//            }
//        }
        return true;
    }

    public function deleteByIntegrationId($id)
    {
        return $this->model->where('integration_id',$id)->delete();
    }

    public function model(): string
    {
        return IntegrationKey::class;
    }
}
