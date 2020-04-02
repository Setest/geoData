<?php

declare (strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\GeoDataHelpers;

class GeoDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $clientIP = $request->ip();
        $request->attributes->set('geoData', GeoDataHelpers::get($clientIP));
        $request->attributes->set('geoDataCountry', GeoDataHelpers::getCountryName($clientIP));
        return $next($request);
    }
}
