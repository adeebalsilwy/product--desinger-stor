<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    /**
     * Generate an API token for the authenticated user
     */
    public function generateToken(Request $request)
    {
        // Check if user is authenticated via web session
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = Auth::user();
        
        // Revoke any existing tokens for this user
        $user->tokens()->delete();
        
        // Create a new token
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'message' => 'API token generated successfully'
        ]);
    }
    
    /**
     * Revoke the user's API token
     */
    public function revokeToken(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        $user = Auth::user();
        
        // Revoke all tokens for the user
        $user->tokens()->delete();
        
        return response()->json([
            'message' => 'API tokens revoked successfully'
        ]);
    }
    
    /**
     * Check if user has a valid API token
     */
    public function checkToken(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['authenticated' => false]);
        }
        
        $user = Auth::user();
        
        // Check if user has any valid tokens
        $hasValidToken = $user->tokens()->exists();
        
        return response()->json([
            'authenticated' => true,
            'has_token' => $hasValidToken,
            'user' => $user->only(['id', 'name', 'email'])
        ]);
    }
}