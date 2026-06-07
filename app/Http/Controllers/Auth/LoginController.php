<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('auth.login');
    }

    public function showPinLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('auth.pin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak sesuai.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda tidak aktif. Hubungi administrator.'],
            ]);
        }

        Auth::login($user, $request->remember ?? false);
        $user->update(['last_login_at' => now()]);

        AuditLog::record('login', $user);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        AuditLog::record('logout', auth()->user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Berhasil logout.');
    }

    // PIN login for cashier quick switch
    public function pinLogin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|min:4|max:6',
        ]);

        $users = User::where('is_active', true)->whereNotNull('pin')->get();

        foreach ($users as $user) {
            if (Hash::check($request->pin, $user->pin)) {
                Auth::login($user);
                $user->update(['last_login_at' => now()]);
                AuditLog::record('pin_login', $user);
                return redirect('/pos');
            }
        }

        return back()->withErrors(['pin' => 'PIN tidak valid.']);
    }
}
