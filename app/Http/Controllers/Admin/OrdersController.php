<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Enums\PaymentStatus;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::when($request->filter && $request->filter != 'all', function ($query) use ($request) {
            $query->where('status', $request->filter);
        })
        ->select('id', 'customer_id', 'status', 'tracking_number', 'payment_status', 'total_amount', 'created_at')
            ->orderBy('created_at', 'desc')
            ->with('customer')
            ->with([
                'products.images' => function ($query) {
                    $query->where('order', 1);
                },
                'tshirts.images' => function ($query) {
                    $query->where('order', 1);
                },
            ])
            ->paginate(10)
            ->withQueryString()
            ->through(function ($order) {
                return [
                    ...$order->toArray(),
                    'created_at' => $order->created_at->format('M d, Y H:i'),
                    'created_at_human' => $order->created_at->diffForHumans(),
                    'total_products' => $order->getTotalProducts(), // Use the newly added method
                    'total_tshirts' => $order->getTotalTshirts(),
                    'total_amount' => $order->getTotalAmount(),
                ];
            });
        $currentFilter = $request->filter ?? 'all';
        return inertia('Admin/Orders', [
            'orders' => $orders, 
            'currentFilter' => $currentFilter,
            'orders_count' => Order::count(),
            'customers_count' => \App\Models\Customer::count(),
            'products_count' => \App\Models\Product::count(),
            'revenue' => Order::sum('total_amount'),
        ]);
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'products.images' => function ($query) {
                $query->where('order', 1);
            },
            'tshirts.images' => function ($query) {
                $query->where('order', 1);
            },
            'items'
        ]);

        return inertia('Admin/OrderDetail', [
            'order' => [
                'id' => $order->id,
                'customer' => $order->customer,
                'status' => $order->status,
                'tracking_number' => $order->tracking_number,
                'payment_status' => $order->payment_status,
                'payment_id' => $order->payment_id,
                'reference_number' => $order->reference_number,
                'transfer_date' => $order->transfer_date,
                'bank_details' => $order->bank_details,
                'receipt_path' => $order->receipt_path,
                'total_tshirts' => $order->getTotalTshirts(),
                'total_products' => $order->getTotalProducts(),
                'total_amount' => $order->getTotalAmount(),
                'created_at' => $order->created_at->format('M d, Y H:i'),
                'created_at_human' => $order->created_at->diffForHumans(),
                'products' => $order->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'main_image' => $product->thumbnail_url ?? '/images/logo.jpeg',
                        'pivot' => [
                            'quantity' => $product->pivot->quantity,
                            'price' => $product->pivot->price,
                            'size' => $product->pivot->size,
                        ]
                    ];
                }),
                'tshirts' => $order->tshirts->map(function ($tshirt) {
                    return [
                        'id' => $tshirt->id,
                        'title' => $tshirt->title,
                        'price' => $tshirt->price,
                        'images' => $tshirt->images,
                        'pivot' => [
                            'quantity' => $tshirt->pivot->quantity,
                            'price' => $tshirt->pivot->price,
                            'size' => $tshirt->pivot->size,
                        ]
                    ];
                }),
            ]
        ]);
    }

    public function update(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required',
            'tracking_number' => 'min:8',
        ]);
        $order = Order::findOrFail($orderId);
        $order->status = $request->status['value'];
        $order->tracking_number = $request->tracking_number;
        $order->save();

        return redirect()->route('admin.orders.index');
    }

    /**
     * Upload receipt for bank transfer order
     */
    public function uploadReceipt(Request $request, $orderId)
    {
        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:5000', // Max 5MB
        ]);

        $order = Order::findOrFail($orderId);

        // Store the receipt image
        $receiptPath = $request->file('receipt')->store('receipts', 'public');

        // Update order with receipt information
        $order->update([
            'receipt_path' => $receiptPath,
            'payment_status' => PaymentStatus::PAID // Mark as paid after receipt upload
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Receipt uploaded successfully'
        ]);
    }
}