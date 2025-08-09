<?php
namespace App\Repositories\Admin;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
class SellerComplaintsRepository extends BaseRepository
{
    public function getAllComplaints(Request $request)
    {
        $searchItem = $request->input('search');

        // Initialize the query with relations and ordering
        $query = $this->model()::with(['customer:id,name','order:id','supportTicketsAttachments'])
            ->orderBy('id', 'desc');

        // Apply search filters if a search term is provided
        if (!empty($searchItem)) {
            $query->where(function ($query) use ($searchItem) {
                $query->whereHas('order', function ($orderQuery) use ($searchItem) {
                    $orderQuery->where('id', $searchItem); // Search by order ID
                })->orWhereHas('customer', function ($customerQuery) use ($searchItem) {
                    $customerQuery->where('name', 'LIKE', "%{$searchItem}%"); // Search by customer name
                });
            });
        }

        // Paginate the results
        return $query->paginate(PAGINATION_COUNT_ADMIN);
    }
    public function getModelById($id)
    {
        return $this->model()::where('id',$id)->first();
    }
    public function model()
    {
        return SupportTicket::class;
    }
}
