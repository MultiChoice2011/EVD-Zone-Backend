<?php
namespace App\Http\Controllers\Admin;

use App\Exports\ProductPricesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequests\ProductMultiDeleteRequest;
use App\Http\Requests\Admin\ProductRequests\ProductRequest;
use App\Http\Requests\ImageUplodeRequest;
use App\Imports\ProductCategoriesImport;
use App\Imports\ProductPriceImport;
use App\Imports\ProductImageImport;
use App\Imports\ProductsImport;
use App\Imports\ProductWholesalePriceImport;
use App\Imports\UpdateProductTranslationImport;
use App\Imports\VendorProductsImport;
use App\Models\ProductImage;
use App\Services\Admin\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public $productService;

    /**
     * Product  Constructor.
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }



    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->productService->getAllProducts($request);
    }


    /**
     * All Cats
     */
    public function get_brand_products(int $brand_id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        return $this->productService->get_brand_products($brand_id);
    }


    /**
     *  Store Product
     * @throws ValidationException
     */
    public function store(ProductRequest $request)
    {
        return $this->productService->storeProduct($request);
    }
    /**
     *  Store Product
     * @throws ValidationException
     */
    public function serials(Request $request)
    {
        return $this->productService->serials($request);
    }
    /**
     *  Store Product
     * @throws ValidationException
     */
    public function applyPriceAll(Request $request)
    {
        return $this->productService->applyPriceAll($request);
    }
    /**
     *  Store Product
     * @throws ValidationException
     */
    public function applyPriceAllGroups(Request $request)
    {
        return $this->productService->applyPriceAllGroups($request);
    }
    public function prices(Request $request)
    {
        return $this->productService->prices($request);
    }

    /**
     * show the product..
     *
     */
    public function show($id)
    {
        return $this->productService->show($id);
    }



    /**
     * Update the product..
     *
     */
    public function update(ProductRequest $request, int $id)
    {
        return $this->productService->updateProduct($request, $id);
    }

    /**
     * changeStatus the product..
     *
     */
    public function changeStatus(Request $request, int $id)
    {
        return $this->productService->changeStatus($request, $id);
    }

    /**
     *
     * Delete Product Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->productService->deleteProduct($id);

    }
    /**
     *
     * Delete Product Using ID.
     *
     */
    public function delete_image_product(int $id)
    {
        return $this->productService->delete_image_product($id);

    }

    public function multiDelete(ProductMultiDeleteRequest $request)
    {
        return $this->productService->multiDelete($request);

    }

    public function productTranslationsImports(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        $withCommit = $request->input('with_commit', 0);
        $withCommit = in_array($withCommit, [0,1]) ? $withCommit : 0;

        Excel::import(new UpdateProductTranslationImport($withCommit), $request->file('file'));

        return response()->json(['message' => 'Products imported successfully!'], 200);

    }

    public function newProductsImports(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        $withCommit = $request->input('with_commit', 0);
        $withCommit = in_array($withCommit, [0,1]) ? $withCommit : 0;

        Excel::import(new ProductsImport($withCommit), $request->file('file'));

        return response()->json(['message' => 'Products imported successfully!'], 200);

    }

    public function productPricesImports(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        $withCommit = $request->input('with_commit', 0);
        $withCommit = in_array($withCommit, [0,1]) ? $withCommit : 0;

        Excel::import(new ProductPriceImport($withCommit), $request->file('file'));

        return response()->json(['message' => 'Products imported successfully!'], 200);

    }


    public function productPricesExport(Request $request)
    {
        return Excel::download(new ProductPricesExport(), 'product_prices.xlsx');

    }

}
