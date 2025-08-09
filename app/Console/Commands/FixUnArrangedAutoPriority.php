<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\DirectPurchase;
use App\Models\DirectPurchasePriority;
use App\Models\Order;
use App\Models\VendorProduct;
use Illuminate\Console\Command;

class FixUnArrangedAutoPriority extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:auto-priority';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix unarranged priority levels for direct purchase priorities';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // VendorProduct::select('product_id')->groupBy('product_id')->chunk(100, function ($products) {
        //     foreach ($products as $product) {
        //         DirectPurchase::create(['product_id' => $product->product_id]);
        //     }
        // });

        DirectPurchase::chunk(100, function ($directPurchases) {
            foreach ($directPurchases as $directPurchase) {
                // Sort priorities by provider_cost in ascending order
                $sortedVendorProducts = VendorProduct::where('product_id', $directPurchase->product_id)
                    ->orderBy('provider_cost')
                    ->get();

                $priorityLevel = 1;
                foreach ($sortedVendorProducts as $vendorProduct) {
                    DirectPurchasePriority::where('vendor_id', $vendorProduct->vendor_id)
                        ->where('direct_purchase_id', $directPurchase->id)
                        ->update(['priority_level' => $priorityLevel++]);
                }
            }
        });

        $this->info('Priority levels have been fixed successfully.');
    }
}
