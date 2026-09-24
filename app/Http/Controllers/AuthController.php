<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'ایمیل الزامی است.',
            'email.email'       => 'ایمیل معتبر نیست.',
            'password.required' => 'رمز عبور الزامی است.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'ایمیل یا رمز عبور اشتباه است.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectAfterLogin($request->user());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectAfterLogin($user)
    {
        // ادمین روستا → پنل روستای خودش
        if ($user->isVillageAdmin() && $user->village_id) {
            return redirect()->intended(route('admin.village.dashboard', $user->village_id));
        }

        // بخشدار → داشبورد اختصاصی بخشداری
        if ($user->isSectionAdmin() && $user->section_id) {
            return redirect()->intended(route('admin.sections.dashboard', $user->section_id));
        }

        // سوپرادمین → پنل مرکزی
        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect('/');
    }
}
