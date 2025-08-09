<?php
namespace App\Repositories\Seller;
use App\Events\WalletRecharged;
use App\Helpers\FileUpload;
use App\Http\Resources\Seller\RechargeBalanceResource;
use App\Models\Seller;
use App\Models\Wallet;
use App\Notifications\CustomNotification;
use App\Services\General\CurrencyService;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use App\Services\Seller\SellerService;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class WalletRepository
{
    use FileUpload,ApiResponseAble;
    public function __construct(public EmailsAndNotificationService $emailsAndNotificationService){}
    public function balanceRecharge($request)
    {
        try{
            if($request['recharge_balance_type'] == 'cash')
            {
                $data = $this->addBalance($request);
                // send notification for admin
                event(new WalletRecharged($data));
                return $this->ApiSuccessResponse(RechargeBalanceResource::make($data),'success message');
            }
            #visa integrate
            return $this->ApiSuccessResponse([],'visa integration');
        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    private function addBalance($data)
    {
        $authSeller = Auth::guard('sellerApi')->user();
        $originalSeller = SellerService::getOriginalSeller($authSeller);
        $balanceData = [
            'recharge_balance_type' => $data['recharge_balance_type'],
            'bank_id' => $data['bank_id'],
            'transferring_name' => $data['transferring_name'],
            'transferring_account_number' => $data['transferring_account_number'],
             'notes' => $data['notes'],
            'amount' => $data['amount'],
            'currency_id' => $data['currency_id'],
            'seller_id' => $originalSeller->id,
            'subseller_id' => $authSeller->parent ? $authSeller->id : null

        ];
        // Check if the 'receipt_image' is provided
        if (isset($data['receipt_image'])) {
            // Use the save_file function to store the image in the 'receipts' directory
            $imagePath = $data['receipt_image'];
            $balanceData['receipt_image'] = $imagePath;  // Store the file path in the database
        }

        return $this->getModel()::create($balanceData);
    }
    public function getBalanceList()
    {
        try{
            $authSeller = Auth::guard('sellerApi')->user();
            $originalSeller = SellerService::getOriginalSeller($authSeller);
            $data = $this->getModel()::query()
            ->with('bank')
            ->where('seller_id', $originalSeller->id)
            ->orderByDesc('id')
            ->get();
            if($data->count() > 0)
                return $this->ApiSuccessResponse(RechargeBalanceResource::collection($data),'success message');
            return $this->ApiErrorResponse([],'data not found');
        }catch (\Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function getTotalAmountOfWallet()
    {
        // Get the authenticated seller
        $authSeller = auth('sellerApi')->user();

        // Fetch the currency conversion rate for the authenticated seller
        $currency = CurrencyService::getCurrentCurrency($authSeller);

        // Ensure the currency conversion rate is valid; default to 1 if not
        $conversionRate = $currency->value ?? 1;
        return number_format($authSeller->balance, 2, '.', '');
    }
    public function getCashTransactions()
    {
        $authSeller = Auth::guard('sellerApi')->user();
        $originalSeller = SellerService::getOriginalSeller($authSeller);
        return $this->getModel()->query()
            ->with(['bank.translations','seller'])
            ->where('seller_id', $originalSeller->id)
            ->where('recharge_balance_type','cash')
            ->whereStatus('pending')
            ->paginate(PAGINATION_COUNT_APP);
    }
    private function getModelById($id)
    {
        return $this->getModel()::find($id);
    }
    private function getModel()
    {
        return new Wallet();
    }
}
