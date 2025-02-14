<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirebaseAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('firebase_user_id')) {
            \Log::info('User not authenticated, redirecting to login');
            return redirect('/login');
        }

        \Log::info('User authenticated, proceeding to request');
        return $next($request);
    }
} 