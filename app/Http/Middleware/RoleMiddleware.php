<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak! Role Anda tidak memiliki izin untuk aksi ini.'
            ], 403);
        }

        return $next($request);
    }
}
