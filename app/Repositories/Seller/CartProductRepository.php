<?php
namespace App\Repositories\Seller;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Traits\ApiResponseAble;

class CartProductRepository
{
    use ApiResponseAble;

    public function storeCartProduct($requestData)
    {
        return $this->getModel()::updateOrCreate(
            [
                'cart_id' => $requestData['cart_id'],
                'product_id' => $requestData['product_id'],
                'category_id' => $requestData['category_id'],
                'type' => $requestData['type'],
            ],
            [
                'quantity' => $requestData['quantity'],
                'is_max_quantity_one' => $requestData['is_max_quantity_one'] ?? 0,
            ]
        );
    }

    public function deleteProduct($cartId, $productId, $categoryId)
    {
        return $this->getModel()::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->where('category_id', $categoryId)
            ->delete();
    }


    private function getModel()
    {
        return CartProduct::class;
    }
    private function getModelById($id)
    {
        return $this->getModel()::find($id);
    }
}
