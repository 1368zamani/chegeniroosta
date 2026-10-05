<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TariffOfficerAuthController extends Controller
{
    /**
     * نمایش فرم ورود.
     *
     * ⚠️ مهم: حتی اگر کاربری logged in باشد، باز هم فرم را نشان می‌دهیم.
     * ورود به پنل کارشناسان همیشه نیاز به نام کاربری و رمز دارد.
     */
    public function showLogin(Request $request)
    {
        // اگر session flag «مسیر کارشناس» هست و کاربر کارشناس است، مستقیم برو
        if (Auth::check() && $request->session()->get('tariff_officer_mode') === true) {
            $u = Auth::user();
            if (in_array($u->role, ['super_admin', 'section_admin', 'fee_officer', 'tariff_officer', 'village_admin'], true)) {
                return redirect()->route('admin.tariffs.index');
            }
        }

        // در غیر این صورت همیشه فرم را نشان بده
        return view('tariff-officer.login', [
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * پردازش ورود.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:150'],
            'password'   => ['required', 'string'],
        ], [
            'identifier.required' => 'نام کاربری یا ایمیل را وارد کنید.',
            'password.required'   => 'رمز عبور را وارد کنید.',
        ]);

        // 🔐 اگر کاربر دیگری logged in است، ابتدا او را خارج کن
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $field = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $field     => $data['identifier'],
            'password' => $data['password'],
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['identifier' => 'نام کاربری/ایمیل یا رمز عبور نادرست است.']);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        $allowed = ['super_admin', 'section_admin', 'fee_officer', 'tariff_officer', 'village_admin'];
        if (!in_array($user->role, $allowed, true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors(['identifier' => 'این حساب دسترسی به پنل تعرفه ندارد.']);
        }

        // ✅ ثبت flag: این نشست از مسیر کارشناسان آمده
        $request->session()->put('tariff_officer_mode', true);
        $request->session()->put('tariff_officer_until', now()->addHours(8)->timestamp);
        $request->session()->put('tariff_officer_ip', $request->ip());
        $request->session()->forget('url.intended');

        return redirect()->route('admin.tariffs.index');
    }

    /**
     * خروج — پاک کردن flag و بازگشت به فرم.
     */
    public function logout(Request $request)
    {
        $request->session()->forget('tariff_officer_mode');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tariff.officer.login');
    }
}
