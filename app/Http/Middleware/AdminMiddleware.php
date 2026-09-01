<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $role = strtolower((string) auth()->user()->role);

        if (!in_array($role, ['spv', 'master'], true)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}