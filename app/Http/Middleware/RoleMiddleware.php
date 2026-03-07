<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $hasRole = false;
        
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}