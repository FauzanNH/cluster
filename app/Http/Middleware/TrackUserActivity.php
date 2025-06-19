<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Update last_active timestamp tanpa mengubah status online
            $user = Auth::user();
            
            // Hanya update last_active jika request bukan dari AJAX polling status
            $path = $request->path();
            if (!str_contains($path, 'user/status')) {
                $user->last_active = now();
                $user->save();
            }
        }
        
        return $next($request);
    }
} 