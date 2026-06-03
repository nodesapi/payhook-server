<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TupplyInternalAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = $request->header('X-Tupply-Secret');
        // Default fallback for local dev is 'tupply-dev-secret-key-12345'
        $expectedSecret = config('services.tupply.secret', 'tupply-dev-secret-key-12345');

        if (!$secret || $secret !== $expectedSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid Tupply Internal Secret Key.',
            ], 401);
        }

        return $next($request);
    }
}
