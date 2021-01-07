<?php

namespace App\Http\Middleware;

use Closure;

class ChangeLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $language = $request->header('accept-language');
        if ($language && in_array($language, ['zh-CN', 'en'])) {
            \App::setLocale($language);
        }

        return $next($request);
    }
}
