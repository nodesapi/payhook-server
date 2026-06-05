<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class CheckKyc
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }

        if ($user->is_admin) {
            return $next($request);
        }

        $tenant = Tenant::where('email', $user->email)->first();

        if (!$tenant) {
            return redirect()->route('tenant.setup');
        }

        if ($tenant->kyc_status === 'PENDING') {
            return redirect()->route('tenant.kyc.pending');
        }

        if ($tenant->kyc_status === 'REJECTED') {
            return redirect()->route('tenant.kyc.rejected');
        }

        return $next($request);
    }
}
