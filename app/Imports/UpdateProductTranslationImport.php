<?php
namespace App\Imports;

use App\Models\Language;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTranslation;
use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Models\Category;
use App\Models\CategoryBrand;
use App\Models\CategoryTranslation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProductTranslationImport implements ToModel, WithHeadingRow, WithChunkReading
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
            $languages = Language::all();  // Fetch all languages

            // Check for existing ProductTranslation by Arabic name (product_name_ar)
            $productTranslation = ProductTranslation::whereRaw("REPLACE(name, ' ', '') = ?", [str_replace(' ', '', trim($row['product_name_ar']))])
                ->orWhereRaw("REPLACE(name, ' ', '') = ?", [str_replace(' ', '', trim($row['product_name_en']))])->first();
            if (! $productTranslation) {
                Log::info('Not found product name ar');
                Log::info($row['product_name_ar']);
                DB::rollback();
                return null;
            }
            Log::info('Product name ar founded');
            Log::info($row['product_name_ar']);

            // Create product translations for each language
            foreach ($languages as $language) {
                // Check if product_name_ar is empty or not set, use product_name_ar if so
                // $productName = empty($row['product_name_ar']) ? $row['product_name_ar'] : $row['product_name_ar'];
                $postfix = '_en';
                if ($language->id == 1) {
                    $postfix = '_ar';
                }

                ProductTranslation::updateOrCreate(
                    [
                        'product_id' => $productTranslation->product_id,
                        'language_id' => $language->id
                    ],
                    [
                        'name' => $row['product_name'.$postfix],
                        'desc' => $row['desc'.$postfix] ?? $row['product_name'.$postfix],
                        'meta_title' => $row['meta_title'.$postfix] ?? $row['product_name'.$postfix],
                        'meta_keyword' => $row['meta_keyword'.$postfix] ?? $row['product_name'.$postfix],
                        'meta_description' => $row['meta_description'.$postfix] ?? $row['product_name'.$postfix],
                        'long_desc' => $row['long_desc'.$postfix] ?? $row['product_name'.$postfix],
                    ]
                );
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
