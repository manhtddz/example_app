<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;
use Symfony\Component\HttpFoundation\Response;

class StoreUrlAfterDoneRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentUrl = $request->fullUrl();

        // Nếu URL hiện tại khác URL cuối cùng trong danh sách thì mới thêm vào (tránh trùng lặp)
        if (empty($previousUrls) || end($previousUrls) !== $currentUrl) {
            $previousUrls[] = $currentUrl;
        }
        Session::push('previous_urls', $previousUrls);

        return $next($request);
    }
}
