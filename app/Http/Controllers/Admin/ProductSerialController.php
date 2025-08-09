<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FillSerialRequests\AutoFillingRequest;
use App\Http\Requests\Admin\FillSerialRequests\ManualFillingRequest;
use App\Http\Requests\Admin\FillSerialRequests\StatusInvoiceSerialRequest;
use App\Http\Requests\Admin\FillSerialRequests\VendorIntegrateRequest;
use App\Services\Admin\ProductSerialService;
use Illuminate\Http\Request;

class ProductSerialController extends Controller
{
    public function __construct(
        private ProductSerialService $productSerialService
    )
    {}


    public function manualFilling(ManualFillingRequest $request)
    {
        return $this->productSerialService->manualFilling($request);
    }
    public function stock_logs(Request $request)
    {
        return $this->productSerialService->stock_logs($request);
    }
    public function stock_logs_invoice($invoice_id)
    {
        return $this->productSerialService->stock_logs_invoice($invoice_id);
    }
    public function update_stock_logs($id , Request $request)
    {
        return $this->productSerialService->update_stock_logs($id, $request);
    }

//    public function vendorIntegrate(VendorIntegrateRequest $request)
//    {
//        return $this->productSerialService->vendorIntegrate($request);
//    }

    public function autoFilling(AutoFillingRequest $request)
    {
        return $this->productSerialService->autoFilling($request);
    }

    public function statusInvoiceSerials(StatusInvoiceSerialRequest $request)
    {
        return $this->productSerialService->statusInvoiceSerials($request);
    }



}
