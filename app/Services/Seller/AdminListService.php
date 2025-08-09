<?php
namespace App\Services\Seller;

use App\Enums\GeneralStatusEnum;
use App\Helpers\FileUpload;
use App\Http\Requests\Seller\UpdateAdminSellerRequest;
use App\Http\Resources\Seller\AdminListResource;
use App\Models\Role;
use App\Models\Seller;
use App\Repositories\Seller\AdminListRepository;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminListService
{
    use ApiResponseAble,FileUpload;
    public function __construct(public AdminListRepository $adminListRepository){}
    public function index($request)
    {
        try{
            $admins = $this->adminListRepository->getAllAdminList($request);
            if($admins->count() > 0)
                return $this->ApiSuccessResponse(AdminListResource::collection($admins));
            return $this->listResponse([]);
        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans("admin.general_error"));
        }
    }
    public function store($request)
    {
        try{
            DB::beginTransaction();
            #create admin user
            $createAdmin = $this->adminListRepository->createAdmin($request);
            DB::commit();
            return $this->ApiSuccessResponse(AdminListResource::make($createAdmin));
        }catch(\Exception $exception){
            DB::rollBack();
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function show($id)
    {
        try{
            $admin = $this->adminListRepository->getModelById($id);
            if($admin)
                return $this->ApiSuccessResponse(AdminListResource::make($admin));
            return $this->notFoundResponse();
        }catch (\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function update($request,$id)
    {
        try{
            $admin = $this->adminListRepository->getModelById($id);
            if(!$admin){
                return $this->ApiErrorResponse([],'admin not found');
            }
            #update admin data
            $updateAdmin = $this->adminListRepository->updateAdmin($id,$request->validated());
            if($updateAdmin){
                return $this->ApiSuccessResponse(AdminListResource::make($admin));
            }

        }catch(\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans("admin.general_error"));
        }
    }
    public function destroy($id)
    {
        try{
            $admin = $this->adminListRepository->getModelById($id);
            if($admin){
                $admin->email .= '-Digital@#$'.time();
                $admin->phone .= '-Digital@#$'.time();
                $admin->status = GeneralStatusEnum::getStatusInactive();
                $admin->delete();
                return $this->ApiSuccessResponse([],'admin seller deleted');
            }
            return $this->ApiErrorResponse([],'You cant delete this id');
        }catch(\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans("admin.general_error"));
        }
    }
    public function updateStatus($id,$request)
    {
        try{
            $admin = $this->adminListRepository->getModelById($id);
            if(!$admin){
                return $this->ApiErrorResponse([],"admin not found");
            }
            #update status
            $admin->update(['status' => $request['status']]);
            return $this->ApiSuccessResponse([],'status is updated');
        }catch(\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans("admin.general_error"));
        }
    }

}
