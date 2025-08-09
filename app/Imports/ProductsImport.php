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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    private $rowNumber = 1;
    private int $withCommit;

    public function __construct(int $withCommit)
    {
        $this->withCommit = $withCommit;
    }

    public function model(array $row)
    {
        $this->rowNumber++;
        Log::info($this->rowNumber);
        // Check if 'product_name_ar' is present and not empty
        if (empty($row['product_name_ar'])) {
            Log::info('not exist-' . $row['product_name_ar']);
            return null;
        }

        DB::beginTransaction();
        try {
            $languages = Language::all();  // Fetch all languages

            $brand = null;
            // Check for existing BrandTranslation by name
            if($row['brand']){
                $row['brand'] = trim($row['brand']);
                $brandTranslation = BrandTranslation::where('name', $row['brand'])->first();
                if (!$brandTranslation) {
                    Log::info('not found brand-' . $row['brand']);
                    // If brand translation not found, create a new brand with 'active' status
                    $brand = Brand::create([
                        'status' => 'active'  // Set the status to 'active' by default
                    ]);

                    // Loop through all languages to create brand translations
                    foreach ($languages as $language) {
                        BrandTranslation::create([
                            'brand_id' => $brand->id,
                            'language_id' => $language->id,
                            'name' => $row['brand'], // Use the name provided in the row or modify as needed
                            'description' => $row['brand'], // Use the description provided in the row or modify as needed
                        ]);
                    }
                } else {
                    // If brand translation found, get the corresponding brand
                    $brand = Brand::find($brandTranslation->brand_id);
                }
            }else{
                Log::info('not exist brand-' . $row['brand']);
                return null;
            }

            // Initialize an array to store category IDs
            $categoryIds = [];
            $categoryId = null;
            // Split the product category into parts
            if($row['product_category']){
                $categories = explode('>', $row['product_category']);
                $parentCategoryId = null;
                $thereAnyBrand = false;
                foreach ($categories as $categoryName) {
                    Log::info('category-' . $categoryName . '-');
                    $categoryName = trim($categoryName);

                    // Check for existing CategoryTranslation by name for the current language
                    $categoryTranslation = CategoryTranslation::where('name', $categoryName)
                        ->whereHas('category', function ($query) use ($parentCategoryId) {
                            $query->where('parent_id', $parentCategoryId);
                        })->first();
                    Log::info('wwwwwwwwwwwwwwwwww');
                    Log::info($categoryTranslation);
                    Log::info($categoryTranslation ? $categoryTranslation->category->parent_id : 'eeeeeeeee');
                    Log::info($parentCategoryId);
                    if ($categoryTranslation && $categoryTranslation->category->parent_id == $parentCategoryId) {
                        Log::info('category-' . $categoryName . '-parent-category-id-' . $categoryTranslation->category_id);
                        $categoryId = $categoryTranslation->category_id;
                        $parentCategoryId = $categoryId;

                    }
                    else{

                        // If category translation not found, create a new category
                        $category = Category::create([
                            'status' => 'active',  // Assuming status comes from the Excel row
                            'web' => 1,            // Assuming these are needed for the category
                            'mobile' => 1,         // Assuming these are needed for the category
                            'parent_id' => $parentCategoryId, // Set parent ID to maintain hierarchy
                            'deleted_at' => null,
                        ]);

                        // Loop through all languages to create category translations
                        foreach ($languages as $language) {
                            CategoryTranslation::create([
                                'category_id' => $category->id,
                                'language_id' => $language->id,
                                'name' => $categoryName, // Use the name for the current part
                                'description' => '', // Set as needed, or leave empty
                            ]);
                        }

                        // Add the new category ID to the array
                        $categoryId = $category->id;

                        // Update parent ID for the next iteration to create a hierarchy
                        $parentCategoryId = $category->id;

                        // relate brand with category
                        if ($categoryName == $row['brand']){
                            $thereAnyBrand = true;
                            Log::info('relate category-' . $categoryName . '-brand-' . $row['brand']);
                            Log::info($category);
                            $category->brand_id = $brand->id;
                            $category->save();
                        }
                    }

                }
                $categoryIds[] = $categoryId;
                if (!$thereAnyBrand) {
                    Log::info('not related-'.$thereAnyBrand);
                }
            }else{
                Log::info('not exist category-' . $row['product_category']);
                return null;
            }

            // Check for existing ProductTranslation by Arabic name (product_name_ar)
            $productTranslation = ProductTranslation::where('name', $row['product_name_ar'])->first();

            $dataForUpdate = ['type' => 'digital'];

            // Check each field in $row individually and add it only if it exists
            if (! isset($productTranslation)) {
                $dataForUpdate['status'] = 'active';
            }
            if (isset($brand)) {
                $dataForUpdate['brand_id'] = $brand->id;
            }
            if (isset($row['price'])) {
                $dataForUpdate['price'] = $row['price'];
            }
            if (isset($row['cost'])) {
                $dataForUpdate['cost_price'] = $row['cost'];
                $dataForUpdate['wholesale_price'] = 11111;
            }
            if (isset($row['notify'])) {
                $dataForUpdate['notify'] = $row['notify'];
            }
            if (isset($row['min'])) {
                $dataForUpdate['minimum_quantity'] = $row['min'];
            }
            if (isset($row['max'])) {
                $dataForUpdate['max_quantity'] = $row['max'];
            }
            if (isset($row['web_status'])) {
                $dataForUpdate['web'] = $row['web_status'];
            }
            if (isset($row['mobile_status'])) {
                $dataForUpdate['mobile'] = $row['mobile_status'];
            }

            if ($productTranslation) {
                Log::info('product-found-' . $this->rowNumber . '-' . $productTranslation->name);
                // If product translation found, fetch the corresponding Product
                $product = Product::find($productTranslation->product_id);

                // Optionally, update the product fields if necessary
                $product->update($dataForUpdate);
            } elseif($brand) {
                // If product not found, create a new Product
                $product = Product::create($dataForUpdate);
            }else{
                DB::rollback();
                return null;
            }

            // Create product translations for each language
            foreach ($languages as $language) {
                // Check if product_name_en is empty or not set, use product_name_ar if so
                // $productName = empty($row['product_name_en']) ? $row['product_name_ar'] : $row['product_name_en'];
                $postfix = '_en';
                if ($language->id == 1) {
                    $postfix = '_ar';
                }
                ProductTranslation::updateOrCreate([
                    'product_id' => $product->id,
                    'language_id' => $language->id,
                ],[
                    'name' => $row['product_name'.$postfix],
                    'desc' => $row['desc'.$postfix] ?? $row['product_name'.$postfix],
                    'meta_title' => $row['meta_title'.$postfix] ?? $row['product_name'.$postfix],
                    'meta_keyword' => $row['meta_keyword'.$postfix] ?? $row['product_name'.$postfix],
                    'meta_description' => $row['meta_description'.$postfix] ?? $row['product_name'.$postfix],
                    'long_desc' => $row['long_desc'.$postfix] ?? $row['product_name'.$postfix],
                ]);
            }

            foreach ($categoryIds as $categoryId) {
                ProductCategory::firstOrCreate([
                    'product_id' => $product->id,
                    'category_id' => $categoryId,
                ]);
            }

            // Commit transaction
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
