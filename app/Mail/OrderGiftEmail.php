<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class OrderGiftEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $productSerials;
    public $orderGift;
    public $customerData;

    public function __construct($productSerials, $orderGift, $authCustomer)
    {
        $this->productSerials = $productSerials;
        $this->orderGift = $orderGift;
        $this->customerData = $authCustomer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            //from: new Address('support@api.multi-choice.com', 'Digital Cart Email'),
            subject: 'Order Gift Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_gift',
        );

        // return (new Content(
        //     view: 'emails.order_gift',
        // ))->with(['productSerials' => $this->productSerials]);

    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
