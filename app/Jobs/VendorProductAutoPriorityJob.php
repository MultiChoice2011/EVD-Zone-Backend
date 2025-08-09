<?php

namespace App\Jobs;

use App\Models\DirectPurchase;
use App\Models\DirectPurchasePriority;
use App\Models\VendorProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class VendorProductAutoPriorityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vendorProductStored;

    public function __construct(VendorProduct $vendorProductStored)
    {
        $this->vendorProductStored = $vendorProductStored;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Create directPurchase if not have any priority
        $directPurchase = DirectPurchase::updateOrCreate(
            ['product_id' => $this->vendorProductStored->product_id]
        );

        // Set initial priority based on the highest current priority level, or start from 0
        $currentMaxPriority = DirectPurchasePriority::select(['direct_purchase_priorities.vendor_id', 'direct_purchase_priorities.priority_level'])
            ->join('vendor_products', 'direct_purchase_priorities.vendor_id', '=', 'vendor_products.vendor_id')
            ->where('direct_purchase_id', $directPurchase->id)
            ->where('direct_purchase_priorities.direct_purchase_id', $directPurchase->id)
            ->where('vendor_products.provider_cost', '<=', $this->vendorProductStored->provider_cost)
            ->orderBy('direct_purchase_priorities.priority_level', 'desc')
            ->first();

        if (! $currentMaxPriority) {
            $currentMaxPriorityNumber = 0;
        }else{
            $currentMaxPriorityNumber = ($currentMaxPriority->vendor_id == $this->vendorProductStored->vendor_id || $currentMaxPriority->priority_level == 1)
                ? 0
                : $currentMaxPriority->priority_level;
        }

        // Fetch vendorProducts with provider_cost >= the new vendor product’s cost
        $vendorProducts = VendorProduct::where('product_id', $this->vendorProductStored->product_id)
            ->where('provider_cost', '>=', $this->vendorProductStored->provider_cost)
            ->orderBy('provider_cost', 'asc')
            ->get();

        // Update the priority levels based on sorted provider costs
        foreach ($vendorProducts as $vp) {
            // Find the related DirectPurchasePriority or create one if it doesn't exist
            DirectPurchasePriority::updateOrCreate(
                ['vendor_id' => $vp->vendor_id, 'direct_purchase_id' => $directPurchase->id],
                ['priority_level' => ++$currentMaxPriorityNumber]
            );
        }

    }
}
