<?php

namespace App\Services\Admin;

use App\Enums\InvoiceType;
use App\Enums\ProductSerialType;
use App\Helpers\FileUpload;
use App\Http\Requests\Admin\ProductRequests\ProductRequest;
use App\Models\ProductImage;
use App\Repositories\Admin\AttributeRepository;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\InvoiceRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\ProductSerialRepository;
use App\Repositories\Admin\VendorRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{

    use ApiResponseAble;


    public function __construct(
        private InvoiceRepository $invoiceRepository,
    )
    {}



}
