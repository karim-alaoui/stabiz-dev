<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

/**
 * This middleware will change the language for each request
 * based on lang header present in the request.
 * This only works for api routes.
 * If no value set, the default will be set.
 * Class LanguageSwitcher
 * @package App\Http\Middleware
 */
class LanguageSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        $defaultLocale = config('app.locale', 'en');
        $lang = $request->header('lang', $defaultLocale);
        if (in_array($lang, ['en', 'ja'])) {
            App::setLocale($lang);
        }
        return $next($request);
    }
}
