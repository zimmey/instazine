<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDeviceToken
{
    /**
     * Require the printer's shared token on device API calls when one is configured.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) config('instazine.device_token');

        if ($token !== '' && ! hash_equals($token, (string) $request->header('X-Device-Token'))) {
            abort(401, 'Missing or invalid device token.');
        }

        return $next($request);
    }
}
