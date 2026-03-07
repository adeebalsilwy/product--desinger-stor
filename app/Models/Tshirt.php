<?php

namespace App\Models;

use App\Helpers\TitleToFolderName;
use Illuminate\Database\Eloquent\Model;

class Tshirt extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'price', 'listed', 'images_folder_name'];

    public function images()
    {
        return $this->hasMany(ShirtImage::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity', 'price')
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
        return $this->images_folder_name ?? TitleToFolderName::convert($this->title) ;
    }

    public function getTotalSales()
    {
        // Calculate total sales, excluding canceled orders
        return $this->orders()
            ->where('status', '!=', 'cancelled')  // Exclude canceled orders
            ->where('payment_status', '=', 'paid') // Only include paid orders
            ->sum('order_tshirt.quantity');      // Sum the quantity from the pivot table
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
