<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BankRequests\StoreRequest;
use App\Services\Admin\BankService;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function __construct(public BankService $bankService){}
    public function index()
    {
        return $this->bankService->index();
    }
    public function store(StoreRequest $request)
    {
        return $this->bankService->store($request->validated());
    }
    public function update($id,StoreRequest $request)
    {
        return $this->bankService->update($id,$request->validated());
    }
    public function destroy($id)
    {
        return $this->bankService->destroy($id);
    }
}
