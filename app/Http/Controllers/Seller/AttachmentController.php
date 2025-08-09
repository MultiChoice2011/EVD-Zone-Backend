<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\AttachmentService;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function __construct(public AttachmentService $attachmentService){}
    public function show($id)
    {
        return $this->attachmentService->show($id);
    }
    public function destroy($id)
    {
        return $this->attachmentService->destroy($id);
    }
}
