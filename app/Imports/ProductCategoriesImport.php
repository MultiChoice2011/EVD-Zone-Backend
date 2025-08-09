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

class ProductCategoriesImport implements ToModel, WithHeadingRow
{
    private $currentRow = 1;

    public function model(array $row)
    {
        $this->currentRow++;
        Log::info($this->currentRow);
        // Check if 'product_name_ar' is present and not empty
        if (empty($row['product_name_ar'])) {
            Log::info('not exist-' . $row['product_name_ar']);
            return null;
        }

        set_time_limit(3600);
        ini_set('memory_limit', '512M');

        DB::beginTransaction();
        try {

            // Split the product category into parts
            if($row['product_category']){
                $categories = array_filter(explode('>', $row['product_category']), fn($value) => $value !== '');
                $categoryName = trim(end($categories));
                Log::info('category-' . $categoryName . '-');

                // Check for existing CategoryTranslation by name for the current language
                $categoryTranslation = CategoryTranslation::where('name', $categoryName)->first();
                if ($categoryTranslation) {
                    // Check for existing ProductTranslation by Arabic name (product_name_ar)
                    $productTranslation = ProductTranslation::where('name', $row['product_name_ar'])->first();
                    if ($productTranslation) {
                        ProductCategory::firstOrCreate([
                            'product_id' => $productTranslation->product_id,
                            'category_id' => $categoryTranslation->category_id,
                        ]);
                    }
                    else{
                        Log::info('product-not-found' . $row['product_name_ar'] . '-' . $this->currentRow);
                        return null;
                    }

                }else{
                    Log::info('category-' . $categoryName . '-parent-category-id-' . $categoryTranslation->category_id);
                    return null;
                }

            }else{
                Log::info('not exist category-' . $row['product_category']);
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
