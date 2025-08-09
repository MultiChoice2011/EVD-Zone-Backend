<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Console\Command;

class RelateCategoriesToBrands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:relate-to-brands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Relate categories with brands based on matching names';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '2G');
        // Fetch all brands into memory
        $brands = Brand::all();

        // Process categories in chunks to avoid memory overload
        Category::chunk(500, function ($categories) use ($brands) {
            foreach ($categories as $category) {
                foreach ($brands as $brand) {
                    // Check if the category name matches the brand name
                    if (strtolower($category->name) == strtolower($brand->name)) {
                        // Update the category with the corresponding brand_id
                        $category->update(['brand_id' => $brand->id]);

                        // Output to the console
                        $this->info("Category '{$category->name}' has been associated with brand '{$brand->name}'.");

                        // Break the loop since a category should only have one brand
                        break;
                    }
                }
            }
        });

        $this->info('Categories have been processed and related to brands.');
    }
}
