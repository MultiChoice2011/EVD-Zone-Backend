<?php
namespace  App\Repositories\Integration;

use App\Models\IntegrationOptionKey;
use Prettus\Repository\Eloquent\BaseRepository;

class IntegrationOptionKeyRepository extends BaseRepository
{

    public function index($request)
    {
        $type = $request->type ?? null;
        $keys = $this->model->query();
        if ($type) {
            $keys = $keys->where('type', $type);
        }
        $keys->select(['key', 'type'])
            ->groupBy(['key', 'type']);
        return $keys->get();
    }

    public function getValue($integrationId, $key)
    {
        return $this->model
            ->where('integration_id', $integrationId)
            ->where('key', $key)
            ->first();
    }

    public function model()
    {
        return IntegrationOptionKey::class;
    }
}
