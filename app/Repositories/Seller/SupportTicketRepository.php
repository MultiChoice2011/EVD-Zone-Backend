<?php
namespace App\Repositories\Seller;

use App\Models\Seller;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Services\Seller\SellerService;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\Auth;

class SupportTicketRepository{
    use ApiResponseAble;
    public function getAllTickets($perPage)
    {
        $authSeller = Auth::guard('sellerApi')->user();
        $originalSeller = SellerService::getOriginalSeller($authSeller);
        return $this->getModel()::where('customer_id', $originalSeller->id)
            ->with('supportTicketsAttachments','order')
            ->orderByDesc('id')
            ->paginate(perPage: $perPage);
    }
    public function create($data){
        $authSeller = Auth::guard('sellerApi')->user();
        $originalSeller = SellerService::getOriginalSeller($authSeller);
        $data['subseller_id'] = $authSeller->parent ? $authSeller->id : null;

        return $originalSeller->SupportTickets()->create($data);
    }
    public function uploadAttachments($data,$ticketId)
    {
        // Find the ticket by its ID
        $ticket = SupportTicket::findOrFail($ticketId);
        if($data){
            // $file = $data['attachments'];
            // $path = $file->store('uploads/support_ticket_attachments', 'public');

            // Save the file path in the ticket_attachments table
            $attachment = new SupportTicketAttachment();
            $attachment->support_ticket_id = $ticket->id;
            $attachment->file_url = $data['attachments'];
            $attachment->extension = null;
            $attachment->size = null;
            $attachment->save();
        }

    }
    public function getTicketsPending()
    {
        $authSeller = Auth::guard('sellerApi')->user();
        $originalSeller = SellerService::getOriginalSeller($authSeller);
        return $this->getModel()->whereStatus('pending')
            ->where('customer_id', $originalSeller->id)
            ->orderByDESC('id')
            ->get();
    }
    private function getModel()
    {
        return new SupportTicket();
    }
}
