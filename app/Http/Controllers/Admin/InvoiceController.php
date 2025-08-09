<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FillSerialRequests\ManualFillingRequest;
use App\Services\Admin\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    )
    {}






}
