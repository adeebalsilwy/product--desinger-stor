<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'email', 'phone', 'country', 'address', 'zipcode'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function totalSpent()
    {
        return $this->orders()
            ->where('status', '!=', 'cancelled')
            ->join('order_tshirt', 'orders.id', '=', 'order_tshirt.order_id')
            ->where('orders.customer_id', $this->id)
            ->sum(DB::raw('order_tshirt.quantity * order_tshirt.price'));
    }

    public function total_orders(){
        $this->loadMissing('orders');
        return $this->orders()->where('status', '!=', 'cancelled')->count();
    }

    public function totalTshirtsBought()
    {
        return $this->orders()
            ->where('status', '!=', 'cancelled')
            ->join('order_tshirt', 'orders.id', '=', 'order_tshirt.order_id')
            ->where('orders.customer_id', $this->id)
            ->sum('order_tshirt.quantity');
    }
}
