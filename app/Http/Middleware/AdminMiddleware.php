<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Debug logging untuk production troubleshooting
        if (config('app.env') === 'production') {
            Log::info('AdminMiddleware - Request to: ' . $request->fullUrl());
            Log::info('AdminMiddleware - User authenticated: ' . ($request->user() ? 'Yes' : 'No'));
            if ($request->user()) {
                Log::info('AdminMiddleware - User role: ' . $request->user()->role);
                Log::info('AdminMiddleware - User email: ' . $request->user()->email);
            }
            Log::info('AdminMiddleware - Session ID: ' . session()->getId());
            Log::info('AdminMiddleware - Has valid session: ' . ($request->hasSession() ? 'Yes' : 'No'));
        }

        // Belum login
        if (! $request->user()) {
            if (config('app.env') === 'production') {
                Log::warning('AdminMiddleware - User not authenticated, redirecting to login');
            }
            // kalau rutenya punya nama login, pakai itu
            return redirect()->route('login');
        }

        // Sudah login tapi bukan admin/superadmin
        if (! in_array($request->user()->role, ['admin', 'superadmin'], true)) {
            if (config('app.env') === 'production') {
                Log::warning('AdminMiddleware - User role not admin/superadmin: ' . $request->user()->role);
            }
            return redirect()->route('dashboard');
        }

        if (config('app.env') === 'production') {
            Log::info('AdminMiddleware - Access granted for user: ' . $request->user()->email);
        }

        return $next($request);
    }
}
