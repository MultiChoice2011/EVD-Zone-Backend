<?php

namespace App\Services\Admin;

use App\Enums\GeneralStatusEnum;
use App\Enums\InvoiceType;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderProductStatus;
use App\Enums\OrderProductType;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\VendorStatus;
use App\Helpers\FileUpload;
use App\Mail\AdminOrderCreatedEmail;
use App\Mail\OrderCreatedEmail;
use App\Models\OrderPaymentReceipt;
use App\Models\Seller;
use App\Notifications\CustomNotification;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\CountryRepository;
use App\Repositories\Admin\DirectPurchaseRepository;
use App\Repositories\Admin\FailedOrderReasonRepository;
use App\Repositories\Admin\IntegrationRepository;
use App\Repositories\Admin\InvoiceRepository;
use App\Repositories\Admin\OrderHistoryRepository;
use App\Repositories\Admin\OrderProductOptionRepository;
use App\Repositories\Admin\OrderProductRepository;
use App\Repositories\Admin\OrderProductSerialRepository;
use App\Repositories\Admin\OrderRepository;
use App\Repositories\Admin\OrderUserRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\ProductSerialRepository;
use App\Repositories\Admin\SellerRepository;
use App\Repositories\Admin\TopupTransactionRepository;
use App\Repositories\Admin\ValueAddedTaxRepository;
use App\Repositories\Admin\VendorProductRepository;
use App\Services\General\FilesServices\CloudinaryService;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use App\Services\General\OnlineShoppingIntegration\IntegrationServiceFactory;
use App\Services\Order\Helpers\CheckVendorBalanceHelper;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{

    use FileUpload, ApiResponseAble;

    public function __construct(
        private OrderRepository                 $orderRepository,
        private SellerRepository                $sellerRepository,
        private OrderProductRepository          $orderProductRepository,
        private OrderProductOptionRepository    $orderProductOptionRepository,
        private TopupTransactionRepository      $topupTransactionRepository,
        private CountryRepository               $countryRepository,
        private CategoryRepository              $categoryRepository,
        private OrderUserRepository             $orderUserRepository,
        private ProductRepository               $productRepository,
        private VendorProductRepository         $vendorProductRepository,
        private IntegrationRepository           $integrationRepository,
        private ValueAddedTaxRepository         $valueAddedTaxRepository,
        private InvoiceRepository               $invoiceRepository,
        private ProductSerialRepository         $productSerialRepository,
        private OrderProductSerialRepository    $orderProductSerialRepository,
        private DirectPurchaseRepository        $directPurchaseRepository,
        private FailedOrderReasonRepository     $failedOrderReasonRepository,
        private OrderHistoryRepository          $orderHistoryRepository,
        private CheckVendorBalanceHelper        $checkVendorBalanceHelper,
        private EmailsAndNotificationService    $emailsAndNotificationService,
        private CloudinaryService               $cloudinaryService,
    )
    {}

    public function getAllOrders($request)
    {
        try {
            $orders = $this->orderRepository->getAllOrders($request);
            return $this->listResponse($orders);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function storeOrder($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get current auth if admin store user_id
            $authAdmin = Auth::guard('adminApi')->user();
            $seller = $this->sellerRepository->showSimple($request->seller_id);
            if (! $seller) {
                return $this->ApiErrorResponse(null, 'Invalid seller id.');
            }
            $orderData = [
                'user_id' => $authAdmin->id,
                'owner_type' => Seller::class,
                'owner_id' => $seller->id,
                'order_status' => OrderStatus::COMPLETED,
                'payment_method' => $request->payment_method,
                'order_source' => OrderSource::DASHBOARD,
                'type' => OrderType::DIRECT,
                'status' => OrderStatus::COMPLETED
            ];
            $createOrder = $this->orderRepository->store($orderData);
            if (! $createOrder){
                return $this->ApiErrorResponse(null, __('admin.general_error'));
            }
            // start processes
            $insertedArray = [];
            $orderTotal = 0;
            foreach ($request->order_products as $orderProduct){
                // get category
                $category = $this->categoryRepository->show($orderProduct['category_id']);
                // get this product
                $product = $this->productRepository->showProductByIdAndCategoryId($orderProduct['product_id'], $orderProduct['category_id']);
                if (! $product || ! $category || $category->is_topup == 0) {
                    return $this->ApiErrorResponse([], 'Invalid product or category.');
                }
                // check for category->is_topup of order product if 0 mean normal order 1 mean topup
                if($category->is_topup == 1){
                    $productTotal = (intval($orderProduct['quantity']) * floatval($product->price));
                    // check if product coins number equal 1 make quantity 1
                    $quantity = $orderProduct['quantity'];
                    if ($product->coins_number == 1){
                        $quantity = 1;
                    }
                    $coinsNumber = $orderProduct['quantity'] * $product->coins_number;
                    $orderProductData = [
                        'order_id' => $createOrder->id,
                        'product_id' => $product->id,
                        'brand_id' => $product->brand_id,
                        'vendor_id' => null,
                        'type' => OrderProductType::getTypeTopUp(),
                        'status' => OrderProductStatus::getTypeCompleted(),
                        'total' => $productTotal,
                        'coins_number' => $coinsNumber,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                    ];
                    $orderTotal += $productTotal;
                    #store order product
                    $orderProductStored = $this->orderProductRepository->store($orderProductData);
                    // store order product option
                    $this->orderProductOptionRepository->store($orderProduct, $product, $orderProductStored->id);

                    $insertedArray[] = $createOrder;
                }
            }
            // Update order total
            $createOrder->update(['total' => $orderTotal,'sub_total' => $orderTotal]);
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $cloudinaryResult = $this->cloudinaryService->uploadFile($request->file('receipt'), 'Order_Receipts');
                if (! $cloudinaryResult){
                    return $this->ApiErrorResponse(null, __('admin.general_error'));
                }
                // $receiptPath = $this->save_file($request->file('receipt'), 'orders/receipts');
                OrderPaymentReceipt::create([
                    'order_id' => $createOrder->id,
                    'file_path' => $cloudinaryResult['secure_url'],
                    'public_id' => $cloudinaryResult['public_id'],
                ]);
            }
            // store history status
            $this->orderHistoryRepository->storeOrderHistoryComplete($createOrder->id);

            DB::commit();
            return $this->ApiSuccessResponse($insertedArray, "Order Created Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $authAdmin = Auth::guard('adminApi')->user();
            $order = $this->orderRepository->show($id);
            if (! $order)
                return $this->notFoundResponse();

            $orderUser = $this->orderUserRepository->checkByOrderIdAndUserId($order->id, $authAdmin->id);
            if ($orderUser)
                $order = $this->orderRepository->formatOrderProductsWithoutHashedTopUp($order);
            else
                $order = $this->orderRepository->formatOrderProductsWithHashedAll($order);

            return $this->showResponse($order);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function update_status(Request $request, int $order_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $order = $this->orderRepository->update_status($data_request, $order_id);
            if ($order)
                return $this->showResponse($order);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function save_notes(Request $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $order = $this->orderRepository->save_notes($data_request);
            if ($order)
                return $this->showResponse($order);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function get_status(Request $request, $status): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $order = $this->orderRepository->get_status($data_request, $status);
            if ($order)
                return $this->showResponse($order);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function get_customer_orders($customer_id): \Illuminate\Http\JsonResponse
    {

        try {
            $order = $this->orderRepository->get_customer_orders($customer_id);
            if ($order)
                return $this->showResponse($order);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


}
