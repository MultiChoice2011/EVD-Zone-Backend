<?php
namespace App\Repositories\Seller;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Traits\ApiResponseAble;

class CartRepository
{
    use ApiResponseAble;
    public function checkProductExistInCar($cartId,$data)
    {
        $cartProduct = CartProduct::where('cart_id', $cartId)
                ->where('product_id', $data['product_id'])
                ->first();
        return $cartProduct;
    }
    public function checkProductQty($data)
    {
        $product = Product::find($data['product_id']);
        // dd($product);
        // Check if the product's stock is zero
        if ($product->quantity <= 0) {
            return $this->ApiErrorResponse([],'This product is currently out of stock.');
        }
        // Check if the requested quantity is greater than available stock
        if ($data['quantity'] > $product->quantity) {
            return $this->ApiErrorResponse([],'Requested quantity exceeds available stock.');
        }
    }
    public function getCart()
    {
        $user = auth('sellerApi')->user();
        $cart = $user->cart()->with([
            'cartProducts.product:id,price,wholesale_price,image',
            'cartProducts.product.product_options',
            'cartProducts.options.cartProductOptionValues',
            'cartProducts.options.optionDetails:options.id,key',
            'cartProducts.options.cartProductOptionValues.optionValue:id,key as option_value_details',
        ])->first();
        return $cart;
    }
    public function show($ownerId, $ownerType)
    {
        return $this->getModel()::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->first();
    }
    public function deleteCart($ownerId, $ownerType)
    {
        return $this->getModel()::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->delete();
    }
    private function getModel()
    {
        return Cart::class;
    }
    private function getModelById($id)
    {
        return $this->getModel()::find($id);
    }
}
