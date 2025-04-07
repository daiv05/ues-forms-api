<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use IvanoMatteo\LaravelDeviceTracking\Facades\DeviceTracker;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->hasDeviceVerified()) {
            return redirect()->route('two-factor.comprobacion');
        }
        return $next($request);
    }
}
