<?php
namespace App\Services\Admin;
use App\Http\Resources\Seller\RechargeBalanceCollection;
use App\Http\Resources\Seller\RechargeBalanceResource;
use App\Models\Seller;
use App\Models\SellerTransaction;
use App\Repositories\Admin\WalletRepository;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\DB;

class WalletService
{
    use ApiResponseAble;
    public function __construct(public WalletRepository $walletRepository){}
    public function getBalanceTransactions($request)
    {
         try{
            $data = $this->walletRepository->getTransactions($request);
            if($data->count() > 0)
                return $this->ApiSuccessResponse(RechargeBalanceCollection::make($data));
            return $this->listResponse([]);
         }catch (\Exception $exception){
             return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error_message'));
         }
    }
    public function show($id)
    {
        try{
            $data = $this->walletRepository->show($id);
            if(!$data){
                return $this->notFoundResponse();
            }
            return $this->ApiSuccessResponse(RechargeBalanceResource::make($data));
        }catch (\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error_message'));
        }
    }
    public function complete($id)
    {
        try{
            DB::beginTransaction();
            $transaction = $this->walletRepository->show($id);
            $seller = Seller::where('id',$transaction->seller_id)->first();
            if(!$transaction)
                return $this->notFoundResponse();
            #convert status to complete
            if($transaction->status != 'pending')
                return $this->ApiErrorResponse([],'transaction status is not pending');
            $transaction->update(['status' => 'complete']);
            #add balance to user auth
            $transactionAmount = number_format(($transaction->amount / $transaction->currency->value), 4, '.', '');
            $seller->balance += doubleval($transactionAmount);
            $seller->save();
            // add record to seller transaction
            SellerTransaction::create([
                'amount' => $transaction->amount,
                'seller_id' => $seller->id,
                'balance' => $seller->balance,
                'type' => 'deposit',
                'currency_id' => $transaction->currency_id
            ]);
            DB::commit();
            return $this->ApiSuccessResponse(RechargeBalanceResource::make($transaction));
        }catch(\Exception $exception)
        {
            DB::rollBack();
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function refused($id)
    {
        try{
            $transaction = $this->walletRepository->show($id);
            if(!$transaction)
                return $this->notFoundResponse();
            #convert status to refused
            if($transaction->status != 'pending')
                return $this->ApiErrorResponse([],'transaction status is not pending');
            $transaction->update(['status' => 'refused']);
            return $this->ApiSuccessResponse(RechargeBalanceResource::make($transaction));
        }catch(\Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
}
