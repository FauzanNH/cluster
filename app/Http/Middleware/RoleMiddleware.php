<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        // Periksa role dengan case insensitive
        if (strtolower(Auth::user()->role) != strtolower($role)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
} 