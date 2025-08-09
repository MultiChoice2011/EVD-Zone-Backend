<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\SellerGroup;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductPricesExport implements FromCollection, WithHeadings, WithCustomChunkSize
{
    protected $sellerGroups;

    public function __construct()
    {
        $this->sellerGroups = SellerGroup::orderBy('id', 'asc')->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $products = Product::with([
            'brand',
            'productPriceSellerGroup' => function ($query) {
                $query->orderBy('seller_group_id', 'asc');
            }
        ])->get();


        return $products->map(function ($product) {
            $row = [
                $product->name,
                optional($product->brand)->name,
                $product->price,
                $product->cost_price,
                $product->wholesale_price,
            ];

            foreach ($this->sellerGroups as $group) {
                $groupPrice = $product->productPriceSellerGroup
                    ->firstWhere('seller_group_id', $group->id);

                $row[] = optional($groupPrice)->price ?? '';
            }

            return $row;
        });

    }

    public function headings(): array
    {
        $groupHeadings = $this->sellerGroups->pluck('name')->toArray();

        $base = [
            'product_name_ar',
            'brand',
            'price',
            'cost',
            'wholesale_price',
        ];

        return array_merge($base, $groupHeadings);
    }

    public function chunkSize(): int
    {
        return 100;
    }

}
