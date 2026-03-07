<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Tshirt;

class HomePageController extends Controller
{
    public function index()
    {
        $tshirts = Tshirt::where('listed', true)->select('id', 'title', 'slug', 'price')->with(['images' => function ($query) {
            $query->where('order', 1)->select('tshirt_id', 'url');
        }])->get();
        return inertia('Customer/HomePage', compact('tshirts'));
    }
}
