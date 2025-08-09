<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\BankService;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function __construct(public BankService $bankService){}
    public function index()
    {
        return $this->bankService->index();
    }
    public function show($id)
    {
        return $this->bankService->show($id);
    }
}
