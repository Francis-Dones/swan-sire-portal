<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireShoreRole
{
    /**
     * Only shore (admin) accounts may pass. Crew accounts are scoped to their own vessel
     * by the API and must not reach user management or exam review actions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = session('user_role') ?? session('api_user.role');

        if ($role !== 'admin') {
            abort(403, 'This action requires a shore (admin) account.');
        }

        return $next($request);
    }
}
