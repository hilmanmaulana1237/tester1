<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanTakeTask
{
    /**
     * Handle an incoming request.
     * Prevents admin users from accessing task-taking routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user can take tasks (admin restriction)
        if (!$user->canTakeTask()) {
            return redirect()->route('dashboard')
                ->with('error', $user->getCannotTakeTaskReason() ?? 'Anda tidak diperbolehkan mengakses halaman ini.');
        }

        return $next($request);
    }
}
