<?php
namespace App\Services\Admin;

use App\Events\ResolveComplain;
use App\Http\Resources\Admin\SellerComplaintsCollection;
use App\Http\Resources\Admin\SellerComplaintsResource;
use App\Models\SupportTicket;
use App\Repositories\Admin\SellerComplaintsRepository;
use App\Traits\ApiResponseAble;
class SellerComplaintsService
{
    use ApiResponseAble;
    public function __construct(public SellerComplaintsRepository $sellerComplaintsRepository){}
    public function index($request)
    {
        try{
            $data = $this->sellerComplaintsRepository->getAllComplaints($request);
            if($data->isNotEmpty())
                return $this->ApiSuccessResponse(SellerComplaintsCollection::make($data),'success message');
            return $this->listResponse([]);
        }catch(\Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function changeStatus($id)
    {
        try{
            $data = $this->sellerComplaintsRepository->getModelById($id);
            if($data){
                $data->update(['status' => 'complete']);
                event(new ResolveComplain($data));
                return $this->ApiSuccessResponse(SellerComplaintsResource::make($data),'status updated success');
            }
            return $this->notFoundResponse();
        }catch(\Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
}
