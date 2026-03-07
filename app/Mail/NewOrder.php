<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrder extends Mailable
{
    use Queueable, SerializesModels;

    protected array $orderDetails;
    protected array $tshirts;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Order $order)
    {
        $this->orderDetails = [
            'customerName' => $this->order->customer->name,
            'customerEmail' => $this->order->customer->email,
            'customerCountry' => $this->order->customer->country,
            'totalTshirts' => $this->order->getTotalTshirts(),
            'totalAmount' => $this->order->getTotalAmount(),
        ];

        $this->tshirts = $this->order->tshirts->map(function ($tshirt) {
            return [
                'title' => $tshirt->title,
                'price' => $tshirt->pivot->price,
                'size' => $tshirt->pivot->size,
                'quantity' => $tshirt->pivot->quantity,
            ];
        })->toArray();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Order',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-order',
            with: [
                'orderDetails' => $this->orderDetails,
                'tshirts' => $this->tshirts,
            ],
        );
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
