<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTranslation;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VendorProductsImport implements ToModel, WithHeadingRow
{
    private $currentRow = 1;

    public function model(array $row)
    {
        $this->currentRow++;
        Log::info($this->currentRow);
        // Check if 'product_name_ar' is present and not empty
        if (empty($row['product_name_ar']) || empty($row['product_vendor_name'])) {
            Log::info('not exist-' . $row['product_name_ar']);
            return null;
        }

        set_time_limit(3600);
        ini_set('memory_limit', '512M');

        DB::beginTransaction();
        try {

            // Check for existing ProductTranslation by Arabic name (product_name_ar)
            $productTranslation = ProductTranslation::with(['product'])->where('name', $row['product_name_ar'])->first();
            $vendor = Vendor::where('name', $row['product_vendor_name'])->first();

            if ($productTranslation && $vendor) {
                Log::info('product-found-' . $this->currentRow . '-' . $productTranslation->name);
                VendorProduct::create([
                    'vendor_id' => $vendor->id,
                    'product_id' => $productTranslation->product_id,
                    'brand_id' => $productTranslation->product->brand_id,
                    'type' => $row['vendor_product_type'],
                    'vendor_product_id' => $row['vendor_product_id'],
                    'provider_cost' => $row['vendor_product_provider_cost'],
                ]);

            }else{
                Log::info('product-not-found-' . $this->currentRow . '-' . $row['product_name_ar'] . '-' . $row['product_vendor_name']);
                DB::rollback();
                return null;
            }


            // Commit transaction
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e; // Optionally handle or log the exception
        }
    }
}
