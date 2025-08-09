<?php
namespace App\Services\Seller\Store;

use App\Http\Resources\Seller\CartResource;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\Seller;
use App\Repositories\Seller\ProductRepository;
use App\Repositories\Seller\CartProductOptionRepository;
use App\Repositories\Seller\CartProductRepository;
use App\Repositories\Seller\CartRepository;
use App\Repositories\Seller\CategoryRepository;
use App\Services\Order\Helpers\CheckVendorBalanceHelper;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService{
    use ApiResponseAble;
    public function __construct(
        public CartRepository                       $cartRepository,
        private CategoryRepository                  $categoryRepository,
        private ProductRepository                   $productRepository,
        private CartProductRepository               $cartProductRepository,
        private CartProductOptionRepository         $cartProductOptionRepository,
        private CheckVendorBalanceHelper            $checkVendorBalanceHelper,
    ){}
    public function index()
    {
        try{
            $cart = $this->cartRepository->getCart();
            return $this->ApiSuccessResponse($cart);
        }catch(Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function store($request)
    {
        try{
            DB::beginTransaction();
            // get category and product to check product options
            $category = $this->categoryRepository->show($request['category_id']);
            if (! $category)
                return $this->ApiErrorResponse(null, 'Product id for category id not found');
            $product = $this->productRepository->getProductDetailsByCategory($request['product_id'], $category);
            if (! $product)
                return $this->ApiErrorResponse(null, 'Product id for category id not found');
            // check availability for vendors
            $availableData =  $this->checkVendorBalanceHelper->checkVendorProductAvailability($product, $category, $request['quantity'], accountData:$request, fromRequest:true);
            if (!$availableData['success'] && $availableData['error_stock']) {
                return $this->ApiErrorResponse(['product_id' => $product->id], __('seller.quantity_not_available'));
            }elseif (!$availableData['success'] && $availableData['error_coding']){
                return $this->ApiErrorResponse(['product_id' => $product->id], __('seller.error_coding'));
            }elseif (!$availableData['success'] && $availableData['error_account_validated']){
                return $this->ApiErrorResponse(['product_id' => $product->id], __('translation.account_not_available'));
            }elseif (!$availableData['success'] && $availableData['general_error']) {
                return $this->ApiErrorResponse();
            }
            if ($category->is_topup == 1 && ($request['product_options'] == null || count($request['product_options']) == 0)) {
                return $this->ApiErrorResponse(null, 'Options needed with category topup.');
            }
            $user = auth('sellerApi')->user();
            $cart = $user->cart()->firstOrCreate();
            $request['cart_id'] = $cart->id;
            // check for category->is_topup of order product if 0 mean serial order 1 mean topup
            if($category->is_topup == 0){
                $request['type'] = 1;
                # Check if the product is already in the cart
                $cartProduct = $this->cartRepository->checkProductExistInCar($cart->id,$request);
                $cartProduct = $this->cartProductRepository->storeCartProduct($request);
            }
            else {
                $request['quantity'] = 1;
                $request['is_max_quantity_one'] = 1;
                $request['type'] = 2;
                $cartProduct = $this->cartProductRepository->storeCartProduct($request);
                $this->cartProductOptionRepository->deleteOptions($cartProduct->id);
                $cartProductOption = $this->cartProductOptionRepository->store($request, $product, $cartProduct->id);
                if(! $cartProductOption)
                    return $this->ApiErrorResponse(null, __('admin.general_error'));
            }
            DB::commit();
            return $this->ApiSuccessResponse(CartResource::make($cart));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->ApiErrorResponse(null,$e);
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function deleteProduct($productId, $categoryId)
    {
        try {
            DB::beginTransaction();
            // get authed Seller
            $authSeller = Auth::guard('sellerApi')->user();
            // get Seller cart
            $cart = $this->cartRepository->show($authSeller->id, Seller::class);
            if (! $cart)
                return $this->ApiErrorResponse(null, 'There is no cart for this customer');
            // delete this product
            $productDeleted = $this->cartProductRepository->deleteProduct($cart->id, $productId, $categoryId);
            if (! $productDeleted)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, "Deleted Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function deleteAll()
    {
        try {
            DB::beginTransaction();
            // get authed Seller
            $authSeller = Auth::guard('sellerApi')->user();
            // delete Seller cart
            $cartDeleted = $this->cartRepository->deleteCart($authSeller->id, Seller::class);
            if (! $cartDeleted)
                return $this->ApiErrorResponse(null, 'There is no cart for this seller');

            DB::commit();
            return $this->ApiSuccessResponse(null, "Deleted Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
