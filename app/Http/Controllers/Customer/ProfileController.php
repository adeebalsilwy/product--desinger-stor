<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\SavedDesign;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the customer's profile edit form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $designs = SavedDesign::with('productType')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();
        
        return Inertia::render('Customer/Profile/Edit', [
            'mustVerifyEmail' => null,
            'status' => session('status'),
            'designs' => $designs,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        
        $user->fill($validated);
        
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $user->save();
        
        return redirect()->route('customer.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        
        $user = $request->user();
        
        Auth::guard('customer')->logout();
        
        $user->delete();
        
        return redirect()->route('home');
    }
}
