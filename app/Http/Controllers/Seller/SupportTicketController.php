<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\CreateSupportTicketRequest;
use App\Services\Seller\SupportTicketService;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function __construct(public SupportTicketService $supportTicketService){}
    public function index()
    {
        return $this->supportTicketService->index();
    }
    public function store(CreateSupportTicketRequest $request)
    {
        return $this->supportTicketService->store($request->validated());
    }
    public function getReplies($id)
    {
        return $this->supportTicketService->getReplies($id);
    }
}
