<?php

namespace App\Jobs;

use App\Mail\NewOrder;
use App\Mail\OrderProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderEmails implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Accessing the order details inside the job
        $customer = $this->order->customer;
        $customerEmail = $customer->email;
        $adminEmail = env('ADMIN_EMAIL');

        Mail::to($customerEmail)->send(new OrderProcessing($customer));
        Mail::to($adminEmail)->send(new NewOrder($this->order));
    }
}
