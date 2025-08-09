<?php

namespace App\Repositories\Admin;

use App\Helpers\FileUpload;
use App\Models\User;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Eloquent\BaseRepository;

class AdminRepository extends BaseRepository
{
    use FileUpload;

    public function __construct(
        Application $app,
        private LanguageRepository $languageRepository
    )
    {
        parent::__construct($app);
    }

    public function getAdmins($requestData)
    {
        $landCode = app()->getLocale() ??'ar';
        $currentLang = $this->languageRepository->getLangByCode($landCode);
        // Default to search value
        $searchTerm = $requestData->input('search', '');
        // Build the base query
        $query = $this->model->query();
        $query->with(['roles', 'permissions']);
        // Exception for the main
        // $query->withoutRole('Super Admin');
        $query->where('email', '<>', 'support@multi-choice.com');
        // Apply searching
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        // Retrieve paginated results
        return $query->latest()->paginate(PAGINATION_COUNT_ADMIN);
    }

    public function getOneAdmin($id)
    {
        return $this->model->with(['roles', 'permissions'])
            ->where('id', $id)
            // ->withoutRole('Super Admin')
            ->where('email', '<>', 'support@multi-choice.com')
            ->first();
    }

    public function storeAdmin($requestData)
    {
        $admin =  $this->model->create([
            'name' => $requestData->name,
            'email' => $requestData->email,
            'phone' => $requestData->phone,
            'password' => bcrypt($requestData->password),
        ]);
        if ($requestData->roles)
            $admin->syncRoles($requestData->roles);
        if ($requestData->permissions)
            $admin->syncPermissions($requestData->permissions);
        return $admin;
    }

    public function updateAdmin($requestData, $id)
    {
        $admin = $this->model
            ->where('id', $id)
            // ->withoutRole('Super Admin')
            ->where('email', '<>', 'support@multi-choice.com')
            ->first();
        if (! $admin)
            return false;
        $admin->name = $requestData->name;
        $admin->email = $requestData->email;
        $admin->phone = $requestData->phone;
        if (isset($requestData->password)) {
            $admin->password = bcrypt($requestData->password);
        }
        $admin->syncRoles($requestData->roles ?? []);
        $admin->syncPermissions($requestData->permissions ?? []);
        $admin->save();
        return $admin;
    }

    public function changeStatus($requestData, $id)
    {
        $admin = $this->model
            ->where('id', $id)
            // ->withoutRole('Super Admin')
            ->where('email', '<>', 'support@multi-choice.com')
            ->first();
        if(!$admin){
            return false;
        }
        // change status
        $admin->status = $requestData->status;
        $admin->save();

        return $admin;
    }

    public function deleteAdmin($id)
    {
        $admin = $this->model
            ->where('id', $id)
            // ->withoutRole('Super Admin')
            ->where('email', '<>', 'support@multi-choice.com')
            ->first();
        if(! $admin)
            return false;
        $admin->delete();
        return true;
    }

    public function getAdminsByPermissions($permissionName)
    {
        return $this->model
            ->where(function ($query) use ($permissionName) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'Super Admin');
                })
                ->orWhereHas('permissions', function ($permissionQuery) use ($permissionName) {
                    $permissionQuery->where('name', $permissionName);
                });
            })->get();
    }


    public function model()
    {
        return User::class;
    }



}
