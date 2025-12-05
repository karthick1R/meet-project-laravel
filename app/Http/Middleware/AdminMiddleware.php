<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized access. Please login first.');
        }

        $user = auth()->user();
        
        // Super admin has access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Regular admin has access to admin routes
        if ($user->isAdmin()) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Admin role required.');
    }
}
