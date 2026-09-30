<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FeeOfficerLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isFeeOfficer()) {
            return redirect()->route('admin.fees.dashboard');
        }
        return view('auth.fees-login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
            throw ValidationException::withMessages([
                'email' => 'ایمیل یا رمز عبور اشتباه است.',
            ]);
        }

        if (!Auth::user()->isFeeOfficer()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'این حساب کاربری از نوع کارشناس مالی/عمرانی نیست.',
            ]);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('admin.fees.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('fees.login');
    }
}
