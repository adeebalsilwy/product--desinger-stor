<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Tshirt;
use Illuminate\Http\Request;

class TshirtsController extends Controller
{
    public function tshirtPage(Request $request, $slug)
    {
        $tshirt = Tshirt::select('id', 'title', 'description', 'price')->where('slug', $slug)->with('images')->first();

        return inertia('Customer/TshirtPage' , compact('tshirt'));
    }
}
