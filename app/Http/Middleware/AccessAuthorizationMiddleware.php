<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $acceptedPosition = [1, 2];

        $employeePosition = auth()->user()->position;
        if (!in_array($employeePosition, $acceptedPosition)) {
            return redirect()->back()->with(SESSION_ERROR, ERROR_FORBIDDEN);
        }
        return $next($request);
    }
}
