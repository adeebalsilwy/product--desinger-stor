<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Jobs\SendOrderEmails;
use App\Mail\NewOrder;
use App\Mail\OrderProcessing;
use App\Models\Customer;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends Controller
{

    public function checkout(Request $request)
    {
        // Validate Checkout Form
        $validated = $request->validate([
            'fullname' => 'required|string|min:2',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|min:2',
            'zipcode' => 'required|integer',
            'country' => 'required|array',
        ]);

        // Get the cart items
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        // Create Customer
        $customer = Customer::create([
            'name' => $validated['fullname'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'zipcode' => $validated['zipcode'],
            'country' => $validated['country']['name'],
        ]);

        // Create an unpaid order
        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::PENDING,
            'tracking_number' => rand(10000000, 99999999),
            'payment_status' => PaymentStatus::UNPAID,
            'payment_id' => '',
        ]);

        // Attach T-shirts from the cart to the order
        $totalTshirts = 0;
        $totalAmount = 0;

        foreach ($cart as $item) {
            $order->tshirts()->attach($item['tshirt_id'], [
                'quantity' => $item['quantity'],
                'price' => $item['tshirt_price'],
                'size' => $item['size'], // Add the size to the pivot table
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Calculate order totals
            $totalTshirts += $item['quantity'];
            $totalAmount += $item['quantity'] * $item['tshirt_price'];
        }

        // Payment Processing
        Stripe::setApiKey(config('services.stripe.secret'));
        $lineItems = [];
        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['tshirt_title'],
                        // 'images' => [$item['tshirt_image']],
                    ],
                    'unit_amount' => $item['tshirt_price'] * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'payment_method_options' => [
                'card' => [
                    'setup_future_usage' => 'on_session', // Prevent saving card
                ],
            ],
            'success_url' => route('thankYou') . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('failedPayment'),
            'ui_mode' => 'hosted',
        ]);

        // Update order totals and add payment session id
        $order->update([
            'total_tshirts' => $totalTshirts,
            'total_amount' => $totalAmount,
            'payment_id' => $checkoutSession->id,
        ]);


        // return redirect()->route('home');
        return response()->json(['url' => $checkoutSession->url]);
    }

    public function thankYouPage(Request $request)
    {
        try {
            $checkoutSessionId = $request->validate([
                'session_id' => 'required|string'
            ])['session_id'];

            $order = Order::where('payment_id', $checkoutSessionId)->firstOrFail();

            if (!$order) {
                return throw new NotFoundHttpException();
            }

            // Use atomic update to prevent double updates
            $updated = Order::where('id', $order->id)
                ->where('payment_status', '!=', PaymentStatus::PAID)
                ->update(['payment_status' => PaymentStatus::PAID]);

            if ($updated) {
                SendOrderEmails::dispatch($order);
                // Log::info('Emails send from redirection');
            }


            session()->forget('cart');

            return inertia('Customer/ThankYouPage');
        } catch (Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->route('home');
        }
    }

    public function failedPaymentPage()
    {
        return inertia('Customer/FailedPaymentPage');
    }

    public function webhook(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $event = null;

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('', 400);
        }
        if ($endpoint_secret) {
            // Only verify the event if there is an endpoint secret defined
            // Otherwise use the basic decoded event
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $sig_header,
                    $endpoint_secret
                );
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                return response('', 400);
            }
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                $paymentId = $paymentIntent->id;

                $order = Order::where('payment_id', $paymentId)->firstOrFail();

                if (!$order) {
                    return response('', 400);
                }

                // Use atomic update to prevent double updates
                $updated = Order::where('id', $order->id)
                ->where('payment_status', '!=', PaymentStatus::PAID)
                ->update(['payment_status' => PaymentStatus::PAID]);

                if ($updated) {
                    SendOrderEmails::dispatch($order);
                    // Log::info('Emails send from webhook');
                }


                session()->forget('cart');

                break;
            default:
                // Unexpected event type
                error_log('Received unknown event type');
        }

        return response('', 200);
    }
}
