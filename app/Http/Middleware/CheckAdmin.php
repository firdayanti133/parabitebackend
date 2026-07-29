<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role_name !== 'admin') {
            return redirect('/admin/login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
