<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tshirt;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\OrderHistory;

class Order extends Model
{
    protected $fillable = ['customer_id', 'number_of_items', 'status', 'tracking_number', 'total_tshirts', 'total_amount', 'payment_status', 'payment_id', 'reference_number', 'transfer_date', 'bank_details', 'receipt_path'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function tshirts()
    {
        // For backward compatibility, map to products table using the new table/column names
        return $this->belongsToMany(Tshirt::class, 'order_product', 'order_id', 'product_id')
            ->withPivot('quantity', 'price', 'size')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
            ->withPivot('quantity', 'price', 'size')
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function getTotalTshirts()
    {
        $this->loadMissing('tshirts');
        return $this->tshirts->sum('pivot.quantity');
    }

    public function getTotalAmount()
    {
        $this->loadMissing('tshirts');
        return $this->tshirts->sum(function ($tshirt) {
            return $tshirt->pivot->quantity * $tshirt->pivot->price;
        });
    }

    public function getTotalProducts()
    {
        $this->loadMissing('products');
        return $this->products->sum('pivot.quantity');
    }

}