<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // If not logged in, just continue
        if (!$user) {
            return $next($request);
        }

        // Skip for admins
        if ($user->is_admin) {
            return $next($request);
        }

        $tenant = Tenant::where('email', $user->email)->first();

        if ($tenant && !$tenant->isSubscriptionActive()) {
            // Allow access to public routes, settings, logout, and the expired view
            if ($request->is('/') || 
                $request->is('pricing*') || 
                $request->is('docs*') || 
                $request->is('tenant/settings*') || 
                $request->is('auth/logout') || 
                $request->is('tenant/subscription-expired')) {
                return $next($request);
            }

            return redirect()->route('tenant.subscription-expired');
        }

        return $next($request);
    }
}
