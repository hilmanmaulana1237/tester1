<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{


    /**
     * Redirect users based on role to admin panel or dashboard.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        if ($user && isset($user->role) && $user->role === 'admin') {
            return redirect('/admin');
        }

        return redirect('/dashboard');
    }
}
