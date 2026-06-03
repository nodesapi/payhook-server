<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if locale query parameter is present (e.g. ?locale=en)
        if ($request->has('locale')) {
            $locale = $request->query('locale');
            if (in_array($locale, ['en', 'id'])) {
                app()->setLocale($locale);
                session(['locale' => $locale]);
            }
        }
        // 2. Check if the URL starts with /en or en/
        elseif ($request->is('en') || $request->is('en/*')) {
            app()->setLocale('en');
            session(['locale' => 'en']);
        }
        // 3. Check if the URL is a public ID route (default /)
        elseif ($request->is('/') || $request->is('pricing') || $request->is('docs') || $request->is('terms') || $request->is('privacy')) {
            app()->setLocale('id');
            session(['locale' => 'id']);
        }
        // 4. Otherwise (e.g., auth pages, admin, tenant), fallback to session locale or default 'id'
        else {
            $locale = session('locale', 'id');
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
