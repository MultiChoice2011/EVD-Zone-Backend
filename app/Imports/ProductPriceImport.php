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
use App\Models\VendorProduct;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductPriceImport implements ToModel, WithHeadingRow, WithChunkReading
{
    private $rowNumber = 2;
    private int $withCommit;

    public function __construct(int $withCommit)
    {
        $this->withCommit = $withCommit;
    }


    public function model(array $row)
    {
        $rowNumber = $this->rowNumber++;
        Log::info('rowNumber: ' . $rowNumber);

        // Check if 'product_name_ar' is present and not empty
        if (empty($row['product_name_ar'])) {
            Log::info('row incorrect product name ar');
            Log::info($row['product_name_ar']);
            return null; // Skip processing if 'product_name_ar' is not found or empty
        }

        DB::beginTransaction();
        try {

            // Check for existing ProductTranslation by Arabic name (product_name_ar)
            $productTranslation =  ProductTranslation::whereRaw("REPLACE(name, ' ', '') = ?", [str_replace(' ', '', trim($row['product_name_ar']))])
                ->orWhere(DB::raw('TRIM(meta_title)'), trim($row['product_name_ar']))->first();
            if ($productTranslation) {
                $product = Product::where('id', $productTranslation->product_id)->first();
                if (! $product) {
                    Log::info('error product id ' . $productTranslation->product_id);
                    DB::rollback();
                    return null; // Skip processing if 'product_name_ar' is not found or empty
                }
                $product->price = $row['price'];
                $product->cost_price = $row['cost'];
                $product->wholesale_price = $row['wholesale_price'];
                $product->save();

                if ($row['onecard'] && $row['onecard'] != '' && is_numeric($row['onecard'])) {
                    $vendorProduct = VendorProduct::where('product_id', $productTranslation->product_id)
                        ->where('vendor_id', 1)->first();
                    if (!$vendorProduct) {
                        Log::info('not found-onecard-price-' . $row['product_name_ar'] . '-product_id-' . $productTranslation->product_id);
                        DB::rollback();
                        return null;
                    }
                    $vendorProduct->update(['provider_cost' => $row['onecard']]);
                }else{
                    Log::info('not exist-onecard-price-' . $row['product_name_ar']);
                }

                if ($row['mintroute'] && $row['mintroute'] != '' && is_numeric($row['mintroute'])) {
                    $vendorProduct = VendorProduct::where('product_id', $productTranslation->product_id)
                        ->where('vendor_id', 2)->first();
                    if (!$vendorProduct) {
                        Log::info('not found-mintroute-price-' . $row['product_name_ar'] . '-product_id-' . $productTranslation->product_id);
                        DB::rollback();
                        return null;
                    }
                    $vendorProduct->update(['provider_cost' => $row['mintroute']]);
                }else{
                    Log::info('not exist-mintroute-price-' . $row['product_name_ar']);
                }


            }else{
                Log::info('Product name ar not founded');
                Log::info($row['product_name_ar']);
                DB::rollback();
                return null;
            }

            if ($this->withCommit){
                DB::commit();
            }

        } catch (\Exception $e) {
            DB::rollback();
            throw $e; // Optionally handle or log the exception
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

}
