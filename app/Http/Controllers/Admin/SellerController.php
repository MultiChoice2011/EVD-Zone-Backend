<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SellerRequests\SellerApprovalStatusRequest;
use App\Http\Requests\Admin\SellerRequests\SellerRequest;
use App\Http\Requests\Admin\SellerRequests\SellerStatusRequest;
use App\Services\Admin\SellerService;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;


class SellerController extends Controller
{
    public function __construct(
        private SellerService $sellerService
    )
    {}


    /**
     * All Sellers
     */
    public function index(Request $request)
    {
        return $this->sellerService->index($request);
    }
    public function notApproved(Request $request)
    {
        return $this->sellerService->notApprovedSellers($request);
    }


    /**
     *  Store Seller
     */
    public function store(SellerRequest $request)
    {
        return $this->sellerService->storeSeller($request);
    }

    /**
     *  Show Seller
     */
    public function show($sellerId)
    {
        return $this->sellerService->showSeller($sellerId);
    }

    /**
     * Update the Seller
     *
     * @throws ValidatorException
     */
    public function update(SellerRequest $request, int $id)
    {
        return $this->sellerService->updateSeller($request, $id);
    }
    /**
     * add_balance the Seller
     *
     * @throws ValidatorException
     */
    public function add_balance(Request $request, int $id)
    {
        return $this->sellerService->add_balance($request, $id);
    }

    public function changeStatus(SellerStatusRequest $request, int $id)
    {
        return $this->sellerService->changeSellerStatus($request, $id);
    }

    public function changeApprovalStatus(SellerApprovalStatusRequest $request, int $id)
    {
        return $this->sellerService->changeSellerApprovalStatus($request, $id);
    }

    public function trash()
    {
        return $this->sellerService->trashSellers();
    }


    public function restore(int $id)
    {
        return $this->sellerService->restoreSeller($id);
    }

    /**
     *
     * Delete Seller Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->sellerService->deleteSeller($id);

    }

    public function deleteAttachments(int $id)
    {
        return $this->sellerService->deleteAttachments($id);

    }

}
