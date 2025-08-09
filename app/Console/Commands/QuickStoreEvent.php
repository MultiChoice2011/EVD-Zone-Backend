<?php

namespace App\Console\Commands;

use App\Enums\OrderProductStatus;
use App\Models\Integration;
use App\Models\OrderProduct;
use App\Models\TopupTransaction;
use App\Services\General\OnlineShoppingIntegration\SadaLiveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuickStoreEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quickstore:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check QuickStore top-up transactions and updates order product status.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $integration = Integration::where('model', SadaLiveService::class)->with(['keys'])->first();
        if (!$integration) {
            Log::error('Integration not found');
        }
        $service = new SadaLiveService($this->formatKeys($integration));

        OrderProduct::with(['topupTransaction'])
            ->where('created_at', '>=', now()->subDays())
            ->where('status', OrderProductStatus::getTypeWaiting())
            ->whereHas('vendor', function ($query) use ($integration) {
                $query->where('integration_id', $integration->id);
            })
            ->whereHas('topupTransaction', function ($query) {
                $query->whereNotNull('integration_order_id');
            })
            ->chunkById(100, function ($orderProducts) use ($service) {
                foreach ($orderProducts as $orderProduct) {
                    $topupTransaction = $orderProduct->topupTransaction;
                    $result = $service->orders([$topupTransaction->integration_order_id]);
                    if (!$result) {
                        continue;
                    }
                    $firstItemStatus = $result['data'][0]['status'];
                    if ($firstItemStatus == 'accept') {
                        $orderProduct->status = OrderProductStatus::getTypeCompleted();
                    }elseif ($firstItemStatus == 'reject') {
                        $orderProduct->status = OrderProductStatus::getTypeRejected();
                    }
                    $orderProduct->save();
                }
            });


    }

    private function formatKeys($integration)
    {
        $formattedKeys = [];
        foreach ($integration->keys as $key) {
            $formattedKeys[$key->key] = $key->value;
        }
        unset($integration['keys']);
        $integration['keys'] = $formattedKeys;
        return $integration;
    }
}
