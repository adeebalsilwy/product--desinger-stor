<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * List user's orders
     */
    public function index(Request $request)
    {
        $orders = Order::with(['items.product', 'items.savedDesign'])
            ->where('customer_id', auth()->id())
            ->latest()
            ->paginate($request->get('per_page', 20));
        
        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Get a specific order
     */
    public function show(Order $order)
    {
        // Check if order belongs to user
        if ($order->customer_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $order->load(['items.product', 'items.savedDesign', 'histories']);
        
        return response()->json([
            'data' => $order,
        ]);
    }

    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.saved_design_id' => 'nullable|exists:saved_designs,id',
            'items.*.customization_data' => 'nullable|array',
            'shipping_address' => 'required|array',
            'billing_address' => 'nullable|array',
            'notes' => 'nullable|string',
            'coupon_code' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Calculate totals
            $subtotal = 0;
            $itemsData = [];
            
            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);
                $unitPrice = $product->final_price;
                $totalPrice = $unitPrice * $item['quantity'];
                
                $subtotal += $totalPrice;
                
                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'product_type_id' => $product->product_type_id,
                    'saved_design_id' => $item['saved_design_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'customization_data' => $item['customization_data'] ?? null,
                ];
            }
            
            // Apply coupon if exists
            $discount = 0;
            if (isset($validated['coupon_code'])) {
                // Implement coupon logic here
            }
            
            // Create order
            $order = Order::create([
                'customer_id' => auth()->id(),
                'status' => 'pending',
                'total_tshirts' => collect($itemsData)->sum('quantity'),
                'total_amount' => $subtotal - $discount,
                'discount_amount' => $discount,
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'payment_status' => 'unpaid',
            ]);
            
            // Create order items
            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }
            
            DB::commit();
            
            // Dispatch job to process order
            dispatch(new \App\Jobs\ProcessOrderItems($order));
            
            return response()->json([
                'data' => $order->load('items'),
                'message' => 'Order created successfully',
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'error' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Order $order)
    {
        // Check if order belongs to user
        if ($order->customer_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'error' => 'Order cannot be cancelled at this stage',
            ], 422);
        }
        
        $order->update(['status' => 'cancelled']);
        
        $order->histories()->create([
            'status_from' => 'pending',
            'status_to' => 'cancelled',
            'notes' => 'Order cancelled by customer',
            'user_id' => auth()->id(),
        ]);
        
        return response()->json([
            'message' => 'Order cancelled successfully',
        ]);
    }

    /**
     * Get order invoice
     */
    public function invoice(Order $order)
    {
        if ($order->customer_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Generate PDF invoice (implement with Dompdf or TCPDF)
        // For now, return order data
        return response()->json([
            'data' => $order->load('items.product'),
        ]);
    }
}
