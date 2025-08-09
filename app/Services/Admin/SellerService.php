<?php

namespace App\Services\Admin;

use App\Enums\GeneralStatusEnum;
use App\Enums\RechargeBalanceTypeEnum;
use App\Enums\SellerApprovalType;
use App\Events\SellerApproved;
use App\Models\SellerTransaction;
use App\Repositories\Admin\SellerAttachmentRepository;
use App\Repositories\Admin\SellerRepository;
use App\Services\Seller\Helpers\SellerHelperService;
use App\Traits\ApiResponseAble;
use Exception;
use App\Helpers\FileUpload;
use App\Models\Currency;
use App\Models\Wallet;
use App\Repositories\Admin\LanguageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class SellerService
{
    use FileUpload, ApiResponseAble;


    public function __construct(
        private SellerRepository                    $sellerRepository,
        private SellerAttachmentRepository          $sellerAttachmentRepository,
        private LanguageRepository                  $languageRepository,
        private SellerHelperService                 $sellerHelperService,
    )
    {}


    public function index($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get all sellers paginated
            $sellers = $this->sellerRepository->index($request);
            if (! $sellers)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($sellers);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function notApprovedSellers($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get all sellers paginated
            $sellers = $this->sellerRepository->notApprovedSellers($request);
            if (! $sellers)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($sellers);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function showSeller($sellerId): JsonResponse
    {
        try {
            DB::beginTransaction();
            // show one seller
            $sellers = $this->sellerRepository->show($sellerId);
            if (! $sellers)
                return $this->ApiErrorResponse(null, 'This id not found');

            DB::commit();
            return $this->showResponse($sellers);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function storeSeller($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get current auth if admin store user_id
            $authAdmin = Auth::guard('adminApi')->user();
            // except image to handling save it
            if (isset($request->logo))
                $request->logo_url = $request->logo;
            // generate random password
            $request->password = generateRandomPassword();
            $request->status = GeneralStatusEnum::getStatusActive();
            $request->user_id = $authAdmin?->id ?? null;
            $request->approval_status = SellerApprovalType::getTypeApproved();
            // get default currency for seller created
            $currency = $this->sellerHelperService->getDefaultSellerCurrency($request['country_id']);
            $request->currency_id = $currency->id;
            // store new seller
            $seller = $this->sellerRepository->store($request);
            if (! $seller)
                return $this->ApiErrorResponse();

            $seller = $this->sellerRepository->makeSuperSeller($seller);

            DB::commit();
            return $this->showResponse($seller);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateSeller($request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get seller details to handle removing old image if new image exists
            $seller = $this->sellerRepository->show($id);
            if(! $seller)
                return $this->notFoundResponse();

            // except image to handling save it
            if (isset($request->logo)){
                // save new image
                $request->logo_url = $request->logo;
            }

            // update new details
            $sellerUpdated = $this->sellerRepository->updateSeller($request, $seller);
            if (! $sellerUpdated)
                return $this->ApiErrorResponse(null, __('admin.general_error'));

            DB::commit();
            return $this->ApiSuccessResponse(null, "Updated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function add_balance(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get seller details to handle removing old image if new image exists
            $seller = $this->sellerRepository->show($id);
            if(! $seller)
                return $this->notFoundResponse();

            $mark_balance = ($request->type == 'withdrawal') ? "-" : "+";
            if ($request->type == 'withdrawal' && $request->balance <= 0)
                return $this->ApiErrorResponse([], __('admin.general_error'));

            $balance = $request->balance;

            if ($mark_balance === '-') {
                $seller->balance -= $balance;
            } else {
                $seller->balance += $balance;
            }
            $seller->save();
            // add transaction seller transaction
            SellerTransaction::create([
                'amount' => $request->balance,
                'seller_id' => $seller->id,
                'note' => $request->note ?? '',
                'balance' => $seller->balance,
                'type' => $request->type ?? '',
                'currency_id' => Currency::Default()->value('id'),
            ]);
            #add transaction in wallet
            Wallet::create([
                'recharge_balance_type' => $request->type == 'deposit' ? RechargeBalanceTypeEnum::ADDED_BY_ADMIN : RechargeBalanceTypeEnum::WITHDRAW_BY_ADMIN,
                'transferring_name' => auth('adminApi')->user()->name,
                'notes' => $request->note ?? '',
                'amount' => $request->balance,
                'type' => $request->type == 'deposit' ? 'add' : 'withdraw',
                'seller_id' => $seller->id,
                'status' => 'complete',
                'currency_id' => $request->currency_id
            ]);
            $seller->load('seller_transactions');
            DB::commit();
            return $this->ApiSuccessResponse($seller, "Added Balance Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeSellerStatus($request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $seller = $this->sellerRepository->changeStatus($request, $id);
            if (! $seller)
                return $this->notFoundResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, "Status Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeSellerApprovalStatus($request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get current auth if admin store user_id
            $authAdmin = Auth::guard('adminApi')->user();
            $request->user_id = $authAdmin?->id ?? null;
            $seller = $this->sellerRepository->changeApprovalStatus($request, $id);
            event(new SellerApproved($seller,$request->approval_status));
            if (! $seller)
                return $this->notFoundResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, "Approval Status Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function deleteSeller(int $sellerId): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            // get seller by id
            $seller = $this->sellerRepository->show($sellerId);
            if (! $seller)
                return $this->notFoundResponse();
            // make soft delete for seller
            $deleted = $this->sellerRepository->destroy($seller);
            if (! $deleted)
                return $this->ApiSuccessResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, "Deleted Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function deleteAttachments(int $attachmentId): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            // get seller by id
            $attachment = $this->sellerAttachmentRepository->deleteById($attachmentId);
            if (! $attachment)
                return $this->notFoundResponse();
            DB::commit();
            return $this->ApiSuccessResponse(null, "Deleted Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function restoreSeller(int $sellerId): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            // restore seller by id
            $seller = $this->sellerRepository->restore($sellerId);

            DB::commit();
            return $this->showResponse($seller);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function trashSellers()
    {
        try {
            DB::beginTransaction();
            // get sellers in trash
            $trashes = $this->sellerRepository->trash();

            DB::commit();
            if (count($trashes) > 0)
                return $this->listResponse($trashes);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


}
