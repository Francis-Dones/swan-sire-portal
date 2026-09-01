<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireShoreRole
{
    /**
     * Only shore accounts may pass: 'admin' (shore reviewer) or 'developer' (a superset
     * that can also toggle mobile modules). Crew accounts are scoped to their own vessel
     * by the API and must not reach user management or exam review actions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = session('user_role') ?? session('api_user.role');

        if (! in_array($role, ['admin', 'developer'], true)) {
            abort(403, 'This action requires a shore (admin) account.');
        }

        return $next($request);
    }
}
