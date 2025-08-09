<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Services\Admin\SellerComplaintsService;
use Illuminate\Http\Request;

class SellerComplaintsController extends Controller
{
    public function __construct(public SellerComplaintsService $sellerComplaintsService){}
    public function index(Request $request)
    {
        return $this->sellerComplaintsService->index($request);
    }
    public function changeStatus($id)
    {
        return $this->sellerComplaintsService->changeStatus($id);
    }
}
