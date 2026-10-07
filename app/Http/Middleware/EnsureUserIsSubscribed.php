<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(isSeller() && auth()->user()->subscription_required == 0) {
            return $next($request);
        }else if(isSeller() && auth()->user()->trail_mode == 1) {
            return $next($request);
        }else if (isSeller() && auth()->user()->is_subscribed == 0) {
            return redirect()->route('subscription.plan.buyPlan')->with('error', 'You are not subscribed to any plan');
        }
        return $next($request);
    }
}
