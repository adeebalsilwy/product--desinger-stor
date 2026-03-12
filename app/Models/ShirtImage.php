<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShirtImage extends Model
{
    protected $fillable = ['order', 'tshirt_id', 'url'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'tshirt_id');
    }

    public function tshirt()
    {
        return $this->belongsTo(Tshirt::class, 'tshirt_id');
    }
}
