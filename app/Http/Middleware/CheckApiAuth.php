<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('api_token') && !auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        return $next($request);
    }
}
