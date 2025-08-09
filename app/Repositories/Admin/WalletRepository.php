<?php
namespace App\Repositories\Admin;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;

class WalletRepository extends BaseRepository
{
    public function getTransactions(Request $request)
    {
        return $this->getModel()::query()->with(['bank','seller','currency'])
        ->when($request->recharge_balance_type,function($q)use($request){
            $q->where('recharge_balance_type',$request->recharge_balance_type);
        })
        ->when($request->status,function($q)use($request){
            $q->where('status',$request->status);
        })
        ->orderByDesc('id')
        ->paginate(PAGINATION_COUNT_ADMIN);
    }
    public function show($id)
    {
        return $this->getModel()::with(['bank','seller'])->where('id',$id)->orderByDesc('id')->first();
    }
    public function model(): string
    {
        return Wallet::class;
    }
}
