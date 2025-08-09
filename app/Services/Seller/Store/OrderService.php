<?php

namespace App\Services\Seller\Store;

use App\Enums\GeneralStatusEnum;
use App\Enums\InvoiceType;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderProductStatus;
use App\Enums\OrderProductType;
use App\Enums\OrderStatus;
use App\Enums\VendorStatus;
use App\Http\Resources\Seller\OrderResource;
use App\Mail\AdminOrderCreatedEmail;
use App\Mail\OrderCreatedEmail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Seller;
use App\Notifications\CustomNotification;
use App\Repositories\Seller\CartRepository;
use App\Repositories\Seller\CategoryRepository;
use App\Repositories\Seller\DirectPurchaseRepository;
use App\Repositories\Seller\OrderRepository;
use App\Repositories\Integration\IntegrationOptionKeyRepository;
use App\Services\General\CurrencyService;
use App\Services\Order\Helpers\CheckVendorBalanceHelper;
use App\Services\Order\Helpers\OrderDependenciesHelper;
use App\Services\Seller\SellerService;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\DB;
use App\Repositories\Seller\FailedOrderReasonRepository;
use App\Repositories\Seller\IntegrationRepository;
use App\Repositories\Seller\InvoiceRepository;
use App\Repositories\Seller\OrderHistoryRepository;
use App\Repositories\Seller\OrderProductOptionRepository;
use App\Repositories\Seller\OrderProductRepository;
use App\Repositories\Seller\OrderProductSerialRepository;
use App\Repositories\Seller\ProductRepository;
use App\Repositories\Seller\ProductSerialRepository;
use App\Repositories\Seller\ValueAddedTaxRepository;
use App\Repositories\Seller\VendorProductRepository;
use App\Repositories\Seller\OrderUserRepository;
use App\Repositories\Seller\SellerRepository;
use App\Repositories\Seller\TopupTransactionRepository;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use App\Services\General\OnlineShoppingIntegration\IntegrationServiceFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Telescope\Telescope;

class OrderService
{
    use ApiResponseAble;

    public function __construct(
        public SellerRepository             $sellerRepository,
        public CartRepository               $cartRepository,
        public OrderRepository              $orderRepository,
        public ProductRepository            $productRepository,
        public OrderProductRepository       $orderProductRepository,
        public OrderProductOptionRepository $orderProductOptionRepository,
        public TopupTransactionRepository   $topupTransactionRepository,
        public FailedOrderReasonRepository  $failedOrderReasonRepository,
        public DirectPurchaseRepository     $directPurchaseRepository,
        public VendorProductRepository      $vendorProductRepository,
        public ProductSerialRepository      $productSerialRepository,
        public OrderProductSerialRepository $orderProductSerialRepository,
        public ValueAddedTaxRepository      $valueAddedTaxRepository,
        public InvoiceRepository            $invoiceRepository,
        public IntegrationRepository        $integrationRepository,
        public OrderUserRepository          $orderUserRepository,
        public OrderHistoryRepository       $orderHistoryRepository,
        public CategoryRepository           $categoryRepository,
        public CheckVendorBalanceHelper     $checkVendorBalanceHelper,
        public EmailsAndNotificationService $emailsAndNotificationService,
        public IntegrationOptionKeyRepository $integrationOptionKeyRepository
    )
    {
    }

    public function index(Request $request)
    {
        try {
            DB::beginTransaction();
            // get authed seller
            $authSeller = Auth::guard('sellerApi')->user();
            $originalSeller = SellerService::getOriginalSeller($authSeller);
            // get orders for auth seller
            $orders = $this->orderRepository->getAllOrders($request, $originalSeller->id);

            DB::commit();
            return $this->showResponse($orders, 'Orders Data...');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function show($id)
    {
        try {
            DB::beginTransaction();
            // get authed seller
            $authSeller = Auth::guard('sellerApi')->user();
            $originalSeller = SellerService::getOriginalSeller($authSeller);
            // get order by id for auth seller
            $order = $this->orderRepository->show($id, $originalSeller->id);
            if (!$order)
                return $this->ApiErrorResponse(null, 'This id not found.');

            DB::commit();
            return $this->showResponse($order, 'Order Data...');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function orderIds()
    {
        try {
            $authSeller = Auth::guard('sellerApi')->user();
            $originalSeller = SellerService::getOriginalSeller($authSeller);
            $orders = Order::whereStatus(OrderStatus::COMPLETED)
                ->where('owner_id', $originalSeller->id)
                ->orderByDesc('id')
                ->pluck('id');
            if ($orders->count() > 0)
                return $this->ApiSuccessResponse($orders);
            return $this->listResponse([]);
        } catch (\Exception $exception) {
            return $this->ApiErrorResponse($exception->getMessage(), trans('admin.general_error'));
        }
    }

    public function storeOrder($request)
    {
        #check seller balance by cart products
        $authSeller = Seller::where('id', Auth::guard('sellerApi')->user()->id)
            ->lockForUpdate()
            ->first();
        Log::info('Authenticated seller retrieved and locked', ['seller_id' => $authSeller->id, 'seller_name' => $authSeller->name ?? 'N/A']);

        $originalSeller = SellerService::getOriginalSeller($authSeller);
        Log::info('Original seller retrieved', ['original_seller_id' => $originalSeller->id, 'original_seller_name' => $originalSeller->name ?? 'N/A']);

        DB::beginTransaction();
        try {
            $cart = $authSeller->cart()->first();
            Log::info('storeOrder - Cart Retrieved', ['cart' => $cart ? $cart->id : null]);
            if (!$cart){
                Log::info('storeOrder - Cart is empty or locked');
                return $this->ApiErrorResponse(null, 'cart is empty.');
            }
            $validatedOrderFees = $this->validateOrderFees($cart, $authSeller);
            Log::info('storeOrder - Order Fees Validated', ['result' => $validatedOrderFees]);
            if ($validatedOrderFees['error']) {
                Log::info('storeOrder - Fee Validation Failed');
                return $this->ApiErrorResponse($validatedOrderFees['data'], $validatedOrderFees['message']);
            }

            $cart = $validatedOrderFees['data'];
            $request->order_status = OrderStatus::PENDING;
            $request->owner_type = Seller::class;
            $request->owner_id = $originalSeller->id;
            $request->subseller_id = $authSeller->parent ? $authSeller->id : null;
            $request->payment_method = OrderPaymentMethod::getBalance();
            Log::info('storeOrder - Order Request Prepared', ['request' => $request->all()]);

            $order = $this->orderRepository->store($cart, $request);
            Log::info('storeOrder - Order Stored', ['order' => $order ? $order->id : null]);
            if (!$order) {
                Log::info('storeOrder - Order Store Failed');
                return $this->ApiErrorResponse(null, __('admin.general_error'));
            }

            #create order products
            $this->makeProcess($cart, $order);
            Log::info('storeOrder - makeProcess Finished', ['order_id' => $order->id]);

            // make order as complete after payment done
            $order->status = OrderStatus::COMPLETED;
            $order->save();
            Log::info('storeOrder - Order Completed', ['order_id' => $order->id]);

            // decrease balance of seller & store transaction data
            $balance = $this->sellerRepository->decreaseBalance($authSeller, $order);
            Log::info('storeOrder - Balance Decreased', ['balanceResult' => $balance]);
            if (!$balance) {
                Log::info('storeOrder - Balance Deduction Failed');
                return $this->ApiErrorResponse(null, __('admin.general_error'));
            }
            // store history status
            $this->orderHistoryRepository->store($order->id);
            Log::info('storeOrder - Order History Stored');

            // delete cart after  finished
            $this->cartRepository->deleteCart($authSeller->id, Seller::class);
            Log::info('storeOrder - Cart Deleted');

            // Telescope tag for complete order
            Telescope::tag(function ($entry) use ($order) {
                return ['web-cart-order-callback-' . $order->id];
            });

            DB::commit();

            // Execute dependencies of orders
            OrderDependenciesHelper::executeDependencies($order);
            Log::info('storeOrder - Dependencies Executed', ['order_id' => $order->id]);

            return $this->ApiSuccessResponse($order, 'Order Created Success');
        } catch (\Exception $e) {
            DB::rollBack();
            // return $this->ApiErrorResponse(null, $e);
            Log::info($authSeller);
            Log::info($e);
            Log::info('something went wrong.n_261');
            return $this->ApiErrorResponse($e->getMessage(), trans('admin.general_error'));
        }
    }

    private function makeProcess(Cart $cart, Order $order): void
    {
        Log::info('makeProcess - Start', ['order_id' => $order->id]);
        // store order's products
        $orderTotalAfterCompleted = 0;
        $orderTotalCost = 0;
        foreach ($cart->cartProducts as $cartProduct) {
            Log::info('makeProcess - Processing CartProduct', ['cart_product' => $cartProduct, 'cart_product_id' => $cartProduct->id]);
            // check if product has not options so that it is normal
            if (count($cartProduct->options) == 0 || $cartProduct->type == 1) {
                Log::info('makeProcess - Detected Serial Product', ['product_id' => $cartProduct->product->id]);
                // that mean normal order with serials
                // order product data for rejected proccess
                $orderProductFailedData = [
                    'product_id' => $cartProduct->product->id,
                    'brand_id' => $cartProduct->product->brand_id,
                    'vendor_id' => null,
                    'type' => OrderProductType::getTypeSerial(),
                    'status' => OrderProductStatus::getTypeRejected(),
                    'total' => 0,
                    'quantity' => 0,
                    'unit_price' => $cartProduct->product->wholesale_price,
                    'order_id' => $order->id,
                ];
                $cartProduct->order_product_type = OrderProductType::getTypeSerial();
                $orderSerialsResult = $this->orderWithSerials($cartProduct, $order);
                Log::info('makeProcess - Serial Product Result', ['result' => $orderSerialsResult]);

                // check availabilty
                // $orderSerialsResult['order_product_serials']
                if ($orderSerialsResult['error']) {
                    Log::info('makeProcess - Serial Order Failed', ['reason' => $orderSerialsResult['error']]);
                    $failedOrderReason = [
                        'order_id' => $order->id,
                        'reason' => $orderSerialsResult['error'],
                    ];
                    $this->failedOrderReasonRepository->store($failedOrderReason);
                    $this->orderProductRepository->store($orderProductFailedData);

                    $orderTotalAfterCompleted += $orderProductFailedData['total'];
                    $orderTotalCost += 0;
                } else {
                    Log::info('makeProcess - Serial Order Succeeded', ['total' => $orderSerialsResult['order_product']['total']]);
                    $orderTotalAfterCompleted += $orderSerialsResult['order_product']['total'];
                    $orderTotalCost += $orderSerialsResult['order_product']['total_cost'];
                }
            } else {
                Log::info('makeProcess - Detected Top-Up Product', ['product_id' => $cartProduct->product->id]);
                // that mean it is topup order
                $orderTopUpResult = $this->orderWithTopUp($cartProduct, $order);
                Log::info('makeProcess - Top-Up Product Result', ['result' => $orderTopUpResult]);

                // check all status
                $orderProductFailedData['type'] = OrderProductType::getTypeTopUp();
                if ($orderTopUpResult['error']) {
                    Log::info('makeProcess - Top-Up Order Failed', ['reason' => $orderTopUpResult['error']]);
                    $failedOrderReason = [
                        'order_id' => $order->id,
                        'reason' => $orderTopUpResult['error'],
                    ];
                    $this->failedOrderReasonRepository->store($failedOrderReason);

                    $orderTotalAfterCompleted += 0;
                    $orderTotalCost += 0;
                } else {
                    Log::info('makeProcess - Top-Up Order Succeeded', ['total' => $orderTopUpResult['order_product']['total']]);
                    $orderTotalAfterCompleted += $orderTopUpResult['order_product']['total'];
                    $orderTotalCost += $orderTopUpResult['order_product']['total_cost'];
                }

            }

        }

        $order->sub_total = $orderTotalAfterCompleted;
        $order->total = $orderTotalAfterCompleted;
        $order->total_cost = $orderTotalCost;
        $order->profit = $orderTotalAfterCompleted - $orderTotalCost;
        $order->save();
        Log::info('makeProcess - Order Totals Updated', [
            'order_id' => $order->id,
            'total' => $order->total,
            'sub_total' => $order->sub_total,
        ]);
        Log::info('makeProcess - End', ['order_id' => $order->id]);
    }

    ///////////////////////////////////////////////////////////////////////
    ////////////////////////////// Assets /////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function orderWithSerials($cartProduct, $order)
    {
        $data = ['order_product' => null, 'order_product_serials' => [], 'error' => null];
        // check if it is live integration or not
        $vendorProduct = null;
        $updateQuantity = false;
        $productSerials = null;
        // get product from direct purchase priorities
        $directPurchase = $this->directPurchaseRepository->showByProductId($cartProduct->product->id);
        // that mean we make this based on priority of live integrations
        if ($directPurchase && $directPurchase->status == GeneralStatusEnum::getStatusActive() && $directPurchase->directPurchasePriorities) {
            $purchaseDone = false;
            $liveIntegrationError = null;
            foreach ($directPurchase->directPurchasePriorities as $directPurchasePriority) {
                $vendorProduct = $this->vendorProductRepository->showByVendorIdAndProductId($directPurchase->product_id, $directPurchasePriority->vendor_id, OrderProductType::getTypeSerial());
                if (!$vendorProduct || $vendorProduct->vendor->integration_id == null || $vendorProduct->vendor->status != VendorStatus::getTypeApproved())
                    continue;
                // make live integration with set serials and invoice
                $liveIntegration = $this->orderWithSerialsLiveIntegration($vendorProduct, $cartProduct);
                if (!$liveIntegration['success']) {
                    Log::info($liveIntegration);
                    $liveIntegrationError = $liveIntegration['error'];
                    continue;
                } else {
                    $productSerials = $liveIntegration['purchase_product_serials'] ?? null;
                    Log::info($liveIntegration);
                    $liveIntegrationError = null;
                    $purchaseDone = true;
                    break;
                }
            }
            if (!$purchaseDone && $liveIntegrationError != null) {
                // that mean it is serials from table because of all priorities false
                // check quantity for product
                if ($cartProduct->product->quantity < $cartProduct->quantity) {
                    $data['error'] = 'Quantity not enough.';
                    return $data;
                }
                // available updating quantity
                $updateQuantity = true;
            }

        } else {      // that mean it is serials from table
            // check quantity for product
            if ($cartProduct->product->quantity < $cartProduct->quantity) {
                $data['error'] = 'Quantity not enough.';
                return $data;
            }
            // available updating quantity
            $updateQuantity = true;
        }

        if (! $productSerials){
            // check if there are available serials
            $productSerials = $this->productSerialRepository->GetFirstExpireFreeSerialsFromProcedure($cartProduct);
            if (!$productSerials) {
                $data['error'] = 'Quantity not enough.';
                return $data;
            }
        }

        // store order products
        $valueAddedTax = $this->valueAddedTaxRepository->show($cartProduct->product->tax_id);
        $tax_value = 0.0000;
        if ($valueAddedTax) {
            if ($cartProduct->product->tax_type == 'fixed')
                $tax_value = ($valueAddedTax->tax_rate / 100) * $cartProduct->product->wholesale_price;
            elseif ($cartProduct->product->tax_type == 'percentage')
                $tax_value = ($valueAddedTax->tax_rate / 100) * $cartProduct->product->tax_amount;
        }

        // check if product coins number equal 1 make quantity 1
        $product = $cartProduct->product;
        $quantity = ($product->coins_number == 1) ? 1 : $productSerials->count();
        $coinsNumber = $productSerials->count() * $product->coins_number;
        if ($quantity == 1 && $product->coins_number == 1){
            $total = ($coinsNumber * $product->wholesale_price);
            $totalCost = ($coinsNumber * $product->cost_price);
        }else{
            $total = ($quantity * $product->wholesale_price);
            $totalCost = ($quantity * $product->cost_price);
        }

        $orderProduct = [
            'product_id' => $cartProduct->product->id,
            'brand_id' => $cartProduct->product->brand_id,
            'vendor_id' => $vendorProduct ? $vendorProduct->vendor_id : null,
            'type' => OrderProductType::getTypeSerial(),
            'status' => OrderProductStatus::getTypeCompleted(),
            'total' => $total,
            'quantity' => $quantity,
            'coins_number' => $coinsNumber,
            'nominal_price' => $cartProduct->product->price,
            'unit_price' => $cartProduct->product->wholesale_price,
            'cost_price' => $product->cost_price,
            'total_cost' => $totalCost,
            'profit' => ($total - $totalCost),
            'tax_value' => $tax_value,
            'order_id' => $order->id,
        ];
        $orderProduct = $this->orderProductRepository->store($orderProduct);
        // store order product's serials
        $orderProductSerials = $this->orderProductSerialRepository->store($productSerials, $orderProduct);
        // change serials to sold
        $serialIds = $productSerials->pluck('id')->toArray();
        $this->productSerialRepository->changeSerialsToSold($serialIds);
        // update quantity value
        if ($updateQuantity)
            $this->productRepository->updateProductQuantity($cartProduct->product->id, $cartProduct->quantity);

        $data['order_product'] = $orderProduct;
        $data['order_product_serials'] = $orderProductSerials;
        return $data;
    }


    public function orderWithSerialsLiveIntegration($vendorProduct, $cartProduct)
    {
        $data = ['purchase_product_serials' => null, 'success' => false, 'error' => null];
        // call method from service
        $vendorIntegrate = $this->integrationRepository->showById($vendorProduct->vendor->integration_id);
        $vendorIntegrate->name = $vendorIntegrate->name == 'mintroute' ? 'mintroute_voucher' : $vendorIntegrate->name;
        $service = IntegrationServiceFactory::create($vendorIntegrate);
        if (!$service) {
            $data['error'] = 'Not found integration service';
            return $data;
            // return false;
        }
        // store invoice for these serials
        $invoice_number = time();
        $invoiceData = [
            'vendor_id' => $vendorProduct->vendor->id,
            'product_id' => $vendorProduct->product->id,
            'user_id' => null,
            'invoice_number' => $invoice_number,
            'type' => InvoiceType::getTypeAuto(),
            'quantity' => 0,
        ];
        $invoice = $this->invoiceRepository->storeInvoice($invoiceData);
        if (!$invoice) {
            $data['error'] = 'Faild when creating invoice';
            return $data;
            // return false;
        }
        // make order from integration
        $requestData = [
            'product_id' => $vendorProduct->vendor_product_id,
            'patch_number' => $invoice_number,
            'quantity' => $cartProduct->quantity,
            'original_product_id' => $vendorProduct->product_id,
            'invoice_id' => $invoice->id,
        ];
        if (!method_exists($service, 'purchaseProduct')) {
            $data['error'] = 'No vendor available.';
            return $data;
        }
        $purchaseProducts = $service->purchaseProduct($requestData);
        if (!$purchaseProducts || count($purchaseProducts['products']) == 0) {
            $this->invoiceRepository->deleteInvoice($invoice->id);
            $data['error'] = 'No free serials in vendor.';
            return $data;
        }
        // store serials
        $data['purchase_product_serials'] = $this->productSerialRepository->store($purchaseProducts['products']);
        // update product and invoice quantity
        $invoice->quantity = $purchaseProducts['quantity'];
        $invoice->price = $purchaseProducts['price'];
        $invoice->save();

        $data['success'] = true;
        return $data;
        // return true;
    }


    public function orderWithTopUp($cartProduct, $order)
    {
        $data = ['order_product' => null, 'order_product_serials' => [], 'error' => null];
        // first store order products
        $valueAddedTax = $this->valueAddedTaxRepository->show($cartProduct->product->tax_id);
        $tax_value = 0.0000;
        if ($valueAddedTax) {
            if ($cartProduct->product->tax_type == 'fixed')
                $tax_value = ($valueAddedTax->tax_rate / 100) * $cartProduct->product->wholesale_price;
            elseif ($cartProduct->product->tax_type == 'percentage')
                $tax_value = ($valueAddedTax->tax_rate / 100) * $cartProduct->product->tax_amount;
        }

        $product = $cartProduct->product;
        $quantity = ($product->coins_number == 1) ? 1 :  $cartProduct->quantity;
        $coinsNumber =  $cartProduct->quantity * $product->coins_number;
        if ($quantity == 1 && $product->coins_number == 1){
            $total = ($coinsNumber * $product->wholesale_price);
            $totalCost = ($coinsNumber * $product->cost_price);
        }else{
            $total = ($quantity * $product->wholesale_price);
            $totalCost = ($quantity * $product->cost_price);
        }

        $orderProduct = [
            'order_id' => $order->id,
            'product_id' => $cartProduct->product->id,
            'brand_id' => $cartProduct->product->brand_id,
            'vendor_id' => null,
            'type' => OrderProductType::getTypeTopUp(),
            'status' => OrderProductStatus::getTypeWaiting(),
            'total' => $total,
            'quantity' => $quantity,
            'coins_number' => $coinsNumber,
            'nominal_price' => $product->price,
            'unit_price' => $product->wholesale_price,
            'cost_price' => $product->cost_price,
            'total_cost' => $totalCost,
            'profit' => ($total - $totalCost),
            'tax_value' => $tax_value,
        ];
        $orderProduct = $this->orderProductRepository->store($orderProduct);
        // store options from selected user
        $this->orderProductOptionRepository->store($cartProduct->options, $orderProduct->id);

        // second check if it is live integration or not
        $vendorProduct = null;
        $productSerials = null;
        $updateQuantity = false;
        // get product from direct purchase priorities
        $directPurchase = $this->directPurchaseRepository->showByProductId($cartProduct->product->id);
        // that mean we make this based on priority of live integrations
        if ($directPurchase && $directPurchase->status == GeneralStatusEnum::getStatusActive() && $directPurchase->directPurchasePriorities) {
            $purchaseDone = false;
            $liveIntegrationError = null;
            $vendorProductSerialsArray = [];
            foreach ($directPurchase->directPurchasePriorities as $directPurchasePriority) {
                // get vendor product as serials to used it after if all topup fails we make it as serials
                $vendorProductSerial = $this->vendorProductRepository->showByVendorIdAndProductId($directPurchase->product_id, $directPurchasePriority->vendor_id, OrderProductType::getTypeSerial());
                if ($vendorProductSerial && $vendorProductSerial->vendor->integration_id != null) {
                    $vendorProductSerialsArray[] = $vendorProductSerial;
                }
                // get first vendor available for this product with id in this vendor integration
                $vendorProduct = $this->vendorProductRepository->showByVendorIdAndProductId($directPurchase->product_id, $directPurchasePriority->vendor_id, OrderProductType::getTypeTopUp());
                // check if it is special-service-vendor
                if (
                    $vendorProduct?->vendor->integration_id == null &&
                    $vendorProduct?->vendor->is_service == 1
                ) {
                    // if product as service it is done without any serials
                    $liveIntegrationError = null;
                    $purchaseDone = true;
                    break;
                }

                if (!$vendorProduct || $vendorProduct->vendor->integration_id == null || $vendorProduct->vendor->status != VendorStatus::getTypeApproved()) {
                    continue;
                }
                // make live integration with set serials and invoice
                $liveIntegration = $this->orderWithTopUpIntegration($vendorProduct, $cartProduct, $orderProduct);
                if (!$liveIntegration['success']) {
                    Log::info($liveIntegration);
                    $liveIntegrationError = $liveIntegration['error'];
                    continue;
                } else {
                    Log::info($liveIntegration);
                    $liveIntegrationError = null;
                    $purchaseDone = true;
                    break;
                }

            }
            if (!$purchaseDone && $liveIntegrationError == null) {
                // here all priorities of topup false we can call now stored as serials
                foreach ($vendorProductSerialsArray as $vendorProductSerial) {
                    // make live integration with set serials and invoice
                    $liveIntegrationSerial = $this->orderWithSerialsLiveIntegration($vendorProductSerial, $orderProduct);
                    Log::info($liveIntegrationSerial);
                    if ($liveIntegrationSerial['success']) {
                        $productSerials = $liveIntegrationSerial['purchase_product_serials'] ?? null;
                        break;
                    }
                }
                // goto else case to continue pull from stock if added from live integration or already exist
                goto subAdminsDecision;
            } elseif ($purchaseDone && $liveIntegrationError == null) {
                $orderProduct->status = $orderProduct->status === OrderProductStatus::getTypeCompleted()
                    ? $orderProduct->status
                    : OrderProductStatus::getTypeWaiting();
                $orderProduct->vendor_id = $vendorProduct ? $vendorProduct->vendor_id : null;
                $orderProduct->save();
            }
            else {
                $data['error'] = $liveIntegrationError;
                // make orderProduct as fail
                $orderProduct->status = OrderProductStatus::getTypeRejected();
                $orderProduct->total = 0;
                $orderProduct->quantity = 0;
                $orderProduct->save();
                return $data;
            }
        } else {
            Log::info('product_id::' . $cartProduct->product_id);
            // that mean it is pass for subAdmins decisions
            // get serial from our stock ( that stored by auto filing from vendor )
            subAdminsDecision:
            if (! $productSerials){
                // check if there are available serials
                $productSerials = $this->productSerialRepository->GetFirstExpireFreeSerialsFromProcedure($cartProduct);
                Log::info('product_id::' . $cartProduct->product_id);
                Log::info($productSerials);
                if (!$productSerials) {
                    $orderProduct->status = OrderProductStatus::getTypeRejected();
                    $orderProduct->total = 0;
                    $orderProduct->quantity = 0;
                    $orderProduct->save();
                    $data['error'] = 'No free serials.';
                    return $data;
                }
                $updateQuantity = true;
            }
            // store order product's serials
            $orderProductSerials = $this->orderProductSerialRepository->store($productSerials, $orderProduct);
            $data['order_product_serials'] = $orderProductSerials;
            // change serials to sold
            $serialIds = $productSerials->pluck('id')->toArray();
            $this->productSerialRepository->changeSerialsToSold($serialIds);
            // update quantity value
            if ($updateQuantity){
                $this->productRepository->updateProductQuantity($cartProduct->product->id, $cartProduct->quantity);
            }
            // update orderProduct quantity and total
            $orderProduct->total = ($productSerials->count() * floatval($cartProduct->product->wholesale_price));
            $orderProduct->quantity = $productSerials->count();
            $orderProduct->save();

            $vendorProduct = $productSerials[0]->invoice ?? null;
        }

        $orderProduct->vendor_id = $vendorProduct ? $vendorProduct->vendor_id : null;
        $orderProduct->save();

        $data['order_product'] = $orderProduct;
        Log::info($data['order_product']);
        return $data;
    }


    public function orderWithTopUpIntegration($vendorProduct, $cartProduct, $orderProduct)
    {
        $data = ['success' => false, 'error' => null];
        // call method from service
        $vendorIntegrate = $this->integrationRepository->showById($vendorProduct->vendor->integration_id);
        $vendorIntegrate->name = $vendorIntegrate->name == 'mintroute' ? 'mintroute_topup' : $vendorIntegrate->name;
        $service = IntegrationServiceFactory::create($vendorIntegrate);
        if (!$service) {
            $data['error'] = 'Not found integration service';
            return $data;
            // return false;
        }
        // first call integration AccountValidation to get details
        $requestData = [];
        $requestData['product_id'] = $vendorProduct->vendor_product_id;     // 3899
        foreach ($cartProduct->options as $option) {
            $optionValueVendorKey = null;
            $optionVendorKey = $this->integrationOptionKeyRepository->getValue($vendorProduct->vendor->integration_id, $option->optionDetails->key);
            if (!$optionVendorKey) {
                $data['error'] = 'options key not found';
                return $data;
            }

            if ($option->cartProductOptionValues && count($option->cartProductOptionValues) > 0){
                $optionValueVendorKey = $this->integrationOptionKeyRepository->getValue($vendorProduct->vendor->integration_id, $option->cartProductOptionValues[0]->optionValue->key);
            }
            // because all integration games want single value
            $requestData[$optionVendorKey->value] = $optionValueVendorKey ? $optionValueVendorKey->value : $option->value;
        }

        // just for me as test
        //$accountInitialization = $service->AccountInitialization($requestData);
        //Log::info($accountInitialization);

        if (!method_exists($service, 'AccountValidation')) {
            $data['error'] = 'Invalid vendor.';
            return $data;
            // return false;
        }
        $accountDetails = $service->AccountValidation($requestData);
        if (!$accountDetails) {
            $data['error'] = 'Account invalid.';
            return $data;
            // return self::ERROR_ACCOUNT_INVALID;
        }

        // second make topup
        $topUpData = [
            'product' => $vendorProduct->product,
            'product_id' => $vendorProduct->vendor_product_id,     // 3899
            'quantity' => 1  //$cartProduct->quantity
        ];

        $topUpData = array_merge($topUpData, $requestData);
        $topUpData = array_merge($topUpData, $accountDetails);

        $topUpDetails = $service->AccountTopUp($topUpData, $cartProduct->product->coins_number);
        if (!$topUpDetails) {
            $data['error'] = 'Vendor not available.';
            return $data;
        }
        if (array_key_exists('is_waiting', $topUpDetails) && $topUpDetails['is_waiting'] == 1) {
            $orderProduct->status = OrderProductStatus::getTypeWaiting();
        }else{
            $orderProduct->status = OrderProductStatus::getTypeCompleted();
        }
        // update quantity by count of vendor executed successfully
        $orderProduct->quantity = $topUpDetails['quantity'];
        $orderProduct->save();
        // store transactions of this topups
        $this->topupTransactionRepository->store($topUpDetails, $vendorProduct->vendor->id, $orderProduct);

        $data['success'] = true;
        return $data;
        // return true;
    }

    public function validateOrderFees(Cart $cart, Seller $seller): array
    {
        $currentCurrency = CurrencyService::getCurrentCurrency($seller);
        // start processes
        $productIdsNotAvailable = [];
        foreach ($cart->cartProducts as $cartProduct) {
            // get this product
            $product = $this->productRepository->showProductByIdAndCategoryId($cartProduct['product_id'], $cartProduct['category_id']);
            if (!$product) {
                return ['data' => null, 'error' => true, 'message' => 'Invalid product id.'];
            }
            $category = $this->categoryRepository->show($cartProduct['category_id']);
            // check availability for vendors
            $availableData = $this->checkVendorBalanceHelper->checkVendorProductAvailability($product, $category, $cartProduct['quantity'], accountData:$cartProduct);
            if (!$availableData['success']) {
                $productIdsNotAvailable[] = $product->id;
            }
        }
        if (count($productIdsNotAvailable) > 0) {
            return ['data' => ['product_ids' => $productIdsNotAvailable], 'error' => true, 'message' => 'Quantity not enough, or not available..!'];
        }
        // depend on seller balance
        Log::info(77777777777);
        Log::info($cart->total_price);
        Log::info($seller->balance);
        if ($seller->balance < $cart->total_price) {
            return ['data' => null, 'error' => true, 'message' => 'Not enough balance.'];
        }
        return ['data' => $cart, 'error' => false, 'message' => ''];
    }

}
