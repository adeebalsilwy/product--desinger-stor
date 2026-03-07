<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClipartCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'slug',
    ];

    public function parent()
    {
        return $this->belongsTo(ClipartCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ClipartCategory::class, 'parent_id');
    }

    public function cliparts()
    {
        return $this->hasMany(Clipart::class, 'clipart_category_id');
    }
}
