<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class CartController extends Controller
{

    public function cartPage()
    {
        return inertia('Customer/CartPage');
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'tshirtId' => 'required|exists:tshirts,id',
            'tshirtTitle' => 'required|string',
            'tshirtPrice' => 'required|numeric|min:0',
            'tshirtImage' => 'required|string',
            'size' => 'required|string|in:XS,S,M,L,XL,XXL',
            'quantity' => 'required|integer|min:1',
        ]);

        $uniqueId = md5($request->tshirtId . $request->size);

        $cart = session()->get('cart', []);

        $isDuplicate = collect($cart)->contains('item_id', $uniqueId);

        if ($isDuplicate) {
            return redirect()->back()->withErrors(['cart' => '"' . $request->size . '"' . ' size of this t-shirt is already in your cart']);
        }

        $cart[] = [
            'item_id' => $uniqueId,
            'tshirt_id' => $validated['tshirtId'],
            'tshirt_title' => $validated['tshirtTitle'],
            'tshirt_image' => $validated['tshirtImage'],
            'tshirt_price' => $validated['tshirtPrice'],
            'size' => $validated['size'],
            'quantity' => $validated['quantity'],
        ];

        session()->put('cart', $cart);
        return redirect()->back();
    }

    public function increaseQuantity(Request $request)
    {
        $item_id = $request->id;
        $cart = session()->get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['item_id'] === $item_id && $item['quantity'] < 10) {
                $cart[$key]['quantity']++;
                session()->put('cart', $cart);
                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    public function decreaseQuantity(Request $request)
    {
        $item_id = $request->id;
        $cart = session()->get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['item_id'] === $item_id && $item['quantity'] > 1) {
                $cart[$key]['quantity']--;
                session()->put('cart', $cart);
                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    public function removeFromCart(Request $request)
    {
        $cart_before = session()->get('cart', []);

        $cart_after = array_filter($cart_before, function ($item) use ($request) {
            return $item['item_id'] !== $request->id;
        });

        // Reindex the array
        $cart_after = array_values($cart_after);

        session()->put('cart', $cart_after);

        return redirect()->back();
    }
}
