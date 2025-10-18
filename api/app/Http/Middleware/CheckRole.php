<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!in_array($request->user()->role->value, $roles)) {
            return response()->json([
                'message' => 'Unauthorized. Required role: ' . implode(' or ', $roles)
            ], 403);
        }

        return $next($request);
    }
}
