<?php
namespace App\Services\Seller;

use App\Http\Resources\Seller\PermissionResource;
use App\Repositories\Seller\PermissionRepository;
use App\Traits\ApiResponseAble;
use Exception;

class PermissionService
{
    use ApiResponseAble;
    public function __construct(public PermissionRepository $permissionRepository){}
    public function index()
    {
        try{
            $permissions = $this->permissionRepository->index();
            if($permissions->count() > 0)
                return $this->ApiSuccessResponse(PermissionResource::collection($permissions), 'permissions data...!');
            return $this->listResponse([]);
        }catch(Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
}
