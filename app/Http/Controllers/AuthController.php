<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->role?->slug;
            if ($role === 'admin') {
                return redirect('/admin');
            }
            if ($role === 'kasir') {
                return redirect('/pos');
            }
            return redirect('/');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();
        if ($user && ! $user->is_active) {
            return back()->withErrors(['login' => 'Akun tidak aktif. Hubungi admin.'])->withInput();
        }

        if (Auth::attempt($data, $request->filled('remember'))) {
            $request->session()->regenerate();
            // Audit log: login
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'auditable_type' => null,
                'auditable_id' => null,
                'description' => 'User logged in',
                'ip_address' => $request->ip(),
            ]);
            $role = Auth::user()->role?->slug;
            if ($role === 'admin') {
                return redirect()->intended('/admin');
            }
            if ($role === 'kasir') {
                return redirect()->intended('/pos');
            }
            return redirect()->intended('/');
        }

        return back()->withErrors(['login' => 'Email atau password salah.'])->withInput();
    }

    public function logout(Request $request)
    {
        // Audit log: logout (if user authenticated)
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'auditable_type' => null,
                'auditable_id' => null,
                'description' => 'User logged out',
                'ip_address' => $request->ip(),
            ]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
