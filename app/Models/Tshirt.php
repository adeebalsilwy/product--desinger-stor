<?php

namespace App\Models;

use App\Helpers\TitleToFolderName;
use Illuminate\Database\Eloquent\Model;

class Tshirt extends Model
{
    protected $table = 'products'; // Map Tshirt model to products table
    protected $fillable = ['name', 'slug', 'description', 'price', 'is_active', 'images_folder_name', 'title'];
    
    protected $casts = [
        'price' => 'decimal:2',  // Ensure price is cast to decimal
    ];

    // Accessor to map 'name' to 'title' for backward compatibility
    public function getTitleAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['title'] ?? null;
    }

    // Mutator to map 'title' to 'name'
    public function setTitleAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    // Accessor to map 'is_active' to 'listed' for backward compatibility
    public function getListedAttribute()
    {
        return $this->attributes['is_active'] ?? null;
    }

    // Mutator to map 'listed' to 'is_active'
    public function setListedAttribute($value)
    {
        $this->attributes['is_active'] = $value;
    }

    public function images()
    {
        return $this->hasMany(ShirtImage::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id')
            ->withPivot('quantity', 'price', 'size')
            ->withTimestamps();
    }

    public function getMainImage()
    {
        return $this->images->where('order', 1)->first();
    }
    public function getOtherImages()
    {
        return $this->images->filter(function ($image) {
            return $image->id !== $this->getMainImage()?->id;
        });
    }

    public function getImagesFolderName()
    {
        return $this->images_folder_name ?? TitleToFolderName::convert($this->getTitleAttribute()) ;
    }

    public function getTotalSales()
    {
        // Calculate total sales, excluding canceled orders
        // Join orders and order_product tables to get the quantity
        return \DB::table('orders')
            ->join('order_product', 'orders.id', '=', 'order_product.order_id')
            ->where('order_product.product_id', $this->id)  // Use product_id instead of tshirt_id
            ->where('status', '!=', 'cancelled')  // Exclude canceled orders
            ->where('payment_status', '=', 'paid') // Only include paid orders
            ->sum('order_product.quantity');      // Sum the quantity from the pivot table
    }

    public function getTotalRevenue()
    {
        // Calculate total revenue, excluding canceled orders
        return $this->orders()
            ->where('status', '!=', 'cancelled')  // Exclude canceled orders
            ->where('payment_status', '=', 'paid') // Only include paid orders
            ->get()                              // Retrieve the filtered orders
            ->sum(function ($order) {
                return $order->pivot->quantity * $order->pivot->price;  // Sum revenue calculation
            });
    }
}