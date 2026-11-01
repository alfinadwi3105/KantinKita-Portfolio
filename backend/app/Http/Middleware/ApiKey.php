<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-API-KEY') !== config('services.api.key')) {
            return response()->json(
                ['message' => 'Kunci API tidak sah.'],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        return $next($request);
    }
}