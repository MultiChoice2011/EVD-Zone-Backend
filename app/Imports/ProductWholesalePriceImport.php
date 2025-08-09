<?php
namespace App\Imports;

use App\Models\Language;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTranslation;
use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductWholesalePriceImport implements ToModel, WithHeadingRow
{
    private $currentRow = 1;

    public function model(array $row)
    {
        $this->currentRow++;
        Log::info($this->currentRow);
        // Check if 'product_name_ar' is present and not empty
        if (empty($row['product_name_ar']) || empty($row['image'])) {
            Log::info('not exist-' . $row['product_name_ar']);
            return null;
        }

        set_time_limit(3600);
        ini_set('memory_limit', '512M');

        DB::beginTransaction();
        try {

            // Check for existing ProductTranslation by Arabic name (product_name_ar)
            $productTranslation = ProductTranslation::with(['product'])->where('name', $row['product_name_ar'])->first();

            if ($productTranslation) {
                if (! $row['wholesale_price']) {
                    Log::info('not exist-price-' . $row['wholesale_price']);
                }
                Log::info('product-found-' . $this->currentRow . '-' . $productTranslation->name);
                Product::where('id', $productTranslation->product_id)->update(['wholesale_price' => $row['wholesale_price']]);

            }else{
                Log::info('product-not-found-' . $this->currentRow . '-' . $row['product_name_ar'] . '-' . $row['product_vendor_name']);
                DB::rollback();
                return null;
            }


            // Commit transaction
            // DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e; // Optionally handle or log the exception
        }
    }

}
