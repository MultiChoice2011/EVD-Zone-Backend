<?php

namespace App\Jobs;

use App\Enums\OrderProductType;
use App\Models\Order;
use App\Services\General\WhatsappIntegration\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;


class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private static $lastExecutionTime = null;

    /**
     * Create a new job instance.
     */
    public function __construct(public $customerPhone, public $message, public ?Order $order = null)
    {
        // $this->onQueue('whatsapp-messaging');
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsappService $whatsappService): void
    {
        // check if send serials for whatsapp
        if (isset($this->order)) {
            $this->sendProductSerialsMessage($whatsappService);
        }else{
            // Call the WhatsApp service to send a message
            $whatsappService->sendMessage(
                $this->customerPhone,
                $this->message
            );
        }
    }

    private function sendProductSerialsMessage(WhatsappService $whatsappService): void
    {
        foreach ($this->order->order_products as $orderProduct) {
            $scratching = '';
            if ($orderProduct->type == OrderProductType::getTypeSerial()) {
                foreach ($orderProduct->orderProductSerials as $orderProductSerial) {
                    $scratching .= $orderProductSerial->scratching . '
                    ';
                }
                $whatsappService->sendMessage(
                    $this->customerPhone,
                    trans("whatsapp.order_product_serial", ['product_name'=> $orderProduct->product->name, 'scratching_codes' => $scratching])
                );
            }

        }
    }
}
