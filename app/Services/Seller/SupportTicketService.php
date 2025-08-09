<?php
namespace App\Services\Seller;

use App\Events\CreateComplain;
use App\Http\Resources\Seller\TicketSystemResource;
use App\Models\SupportTicket;
use App\Notifications\CustomNotification;
use App\Repositories\Seller\SupportTicketRepository;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\DB;

class SupportTicketService
{
    use ApiResponseAble;
    public function __construct(public SupportTicketRepository $supportTicketRepository,public EmailsAndNotificationService $emailsAndNotificationService){}
    public function index($perPage = 15)
    {
        $tickets = $this->supportTicketRepository->getAllTickets($perPage);
        return $this->ApiSuccessResponse(TicketSystemResource::collection($tickets));
    }
    public function store($request)
    {
        try{
            DB::beginTransaction();
            $ticket = $this->supportTicketRepository->create($request);
            #upload attachment
            // Upload multiple attachments if they exist
            if (isset($request['attachments'])) {
                $this->supportTicketRepository->uploadAttachments($request,$ticket->id);
            }
            // send notification for admin
            event(new CreateComplain($ticket));
            DB::commit();
            return $this->ApiSuccessResponse(TicketSystemResource::make($ticket));
        }catch(\Exception $e){
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function getReplies($id)
    {
        $ticket = SupportTicket::with('replies')->find($id);
        if($ticket)
            return $this->ApiSuccessResponse(TicketSystemResource::make($ticket));
        return $this->ApiErrorResponse([],'ticket not');
    }
}
