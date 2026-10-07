<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(isSeller() && auth()->user()->subscription_required == 0) {
            return $next($request);
        }else if(isSeller() && auth()->user()->trail_mode == 1) {
            return $next($request);
        }else if (isSeller() && auth()->user()->is_subscribed == 0) {
            return sendError('You are not subscribed to any plan', [], 403);
        }
        return $next($request);
    }
}
