<?php

namespace App\Repositories\Admin;

use App\Enums\OrderComplaintStatus;
use App\Models\OrderComplaint;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderComplaintRepository extends BaseRepository
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }


    public function index($requestData)
    {
        $validSortColumns = ['created_at', 'order_id', 'status', 'customer_name', 'customer_phone'];
        $sortBy = in_array($requestData->input('sort_by'), $validSortColumns, true) ? $requestData->input('sort_by') : 'created_at';
        $sortDirection = $requestData->input('sort_direction', 'desc');
        $searchTerm = $requestData->input('search', '');

        $query = $this->model->query()
            ->join('customers', 'order_complaints.customer_id', '=', 'customers.id')
            ->select(
                'order_complaints.id',
                'order_complaints.customer_id',
                'order_complaints.order_id',
                'order_complaints.description',
                'order_complaints.status',
                'order_complaints.created_at',
                'order_complaints.updated_at'
            )
            ->with(['customer:id,name,phone', 'order:id,status,payment_method,total']);

        switch ($sortBy) {
            case 'customer_name':
                $query->orderBy('customers.name', $sortDirection);
                break;
            case 'customer_phone':
                $query->orderBy('customers.phone', $sortDirection);
                break;
            default:
                $query->orderBy($sortBy, $sortDirection);
                break;
        }

        $query->where(function (Builder $queryBuilder) use ($searchTerm) {
            $queryBuilder->where('customers.name', 'like', '%' . $searchTerm . '%')
                ->orWhere('order_complaints.order_id', 'like', '%' . $searchTerm . '%');
        });

        return $query->paginate(PAGINATION_COUNT_ADMIN);
    }

    public function changeStatus($id)
    {
        $orderComplaint = $this->model
            ->where('id', $id)
            ->first();
        $orderComplaint->status = OrderComplaintStatus::getTypeCompleted();
        $orderComplaint->save();
        return $orderComplaint;
    }


    public function model(): string
    {
        return OrderComplaint::class;
    }
}
