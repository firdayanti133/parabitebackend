<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role_name === 'admin') {
            return redirect('/admin/dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role_name === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended('/admin/dashboard');
            }

            Auth::logout();
            return back()->with('error', 'You do not have admin access.');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }

    public function dashboard()
    {
        $totalUsers = \App\Models\User::count();
        $adminUsers = \App\Models\User::where('role_name', 'admin')->count();
        $regularUsers = \App\Models\User::where('role_name', 'user')->count();
        $merchantUsers = \App\Models\User::where('role_name', 'merchant')->count();
        $recentUsers = \App\Models\User::orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'adminUsers', 'regularUsers', 'merchantUsers', 'recentUsers'));
    }
}
