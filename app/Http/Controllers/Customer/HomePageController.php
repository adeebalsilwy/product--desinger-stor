<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Tshirt;

class HomePageController extends Controller
{
    public function index()
    {
        $tshirts = Tshirt::where('is_active', true)->with(['images' => function ($query) {
            $query->where('order', 1);
        }])->get();
        return inertia('Customer/HomePage', compact('tshirts'));
    }
}
