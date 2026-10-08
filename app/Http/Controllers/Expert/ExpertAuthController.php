<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureExpertAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && in_array(Auth::user()->role, EnsureExpertAccess::ALLOWED_ROLES, true)) {
            return redirect()->route('expert.dashboard');
        }

        return view('expert.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:150'],
            'password'   => ['required', 'string'],
        ], [
            'identifier.required' => 'نام کاربری یا ایمیل را وارد کنید.',
            'password.required'   => 'رمز عبور را وارد کنید.',
        ]);

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $field = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$field => $data['identifier'], 'password' => $data['password']];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['identifier' => 'نام کاربری یا رمز عبور نادرست است.']);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if (!in_array($user->role, EnsureExpertAccess::ALLOWED_ROLES, true)) {
            Auth::logout();
            $request->session()->invalidate();
            return back()->withErrors(['identifier' => 'شما دسترسی به پنل کارشناسان ندارید.']);
        }

        $request->session()->put('expert_mode', true);
        $request->session()->put('expert_since', now()->timestamp);

        return redirect()->intended(route('expert.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('expert.login');
    }
}
