<?php

if (!function_exists('localeRoute')) {
    /**
     * Generate a localized URL for a named route.
     *
     * @param  string  $name
     * @param  mixed   $parameters
     * @return string
     */
    function localeRoute($name, $parameters = [])
    {
        $locale = app()->getLocale();
        if ($locale === 'en') {
            $enRoute = 'en.' . $name;
            if (\Illuminate\Support\Facades\Route::has($enRoute)) {
                return route($enRoute, $parameters);
            }
        }
        return route($name, $parameters);
    }
}
