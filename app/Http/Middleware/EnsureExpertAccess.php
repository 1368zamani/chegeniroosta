<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureExpertAccess
{
    /** نقش‌های مجاز به پنل کارشناسان */
    public const ALLOWED_ROLES = [
        'super_admin',      // مدیر ارشد
        'section_admin',    // ادمین بخش
        'fee_officer',      // کارشناس مالی
        'tariff_officer',   // کارشناس تعرفه
        'village_admin',    // ادمین روستا (فقط برای تعرفه‌های روستای خودش)
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('expert.login')
                ->withErrors(['identifier' => 'ابتدا وارد شوید.']);
        }

        if (!in_array($user->role, self::ALLOWED_ROLES, true)) {
            Auth::logout();
            return redirect()->route('expert.login')
                ->withErrors(['identifier' => 'شما دسترسی به پنل کارشناسان ندارید.']);
        }

        // چک کردن وضعیت فعال بودن
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            return redirect()->route('expert.login')
                ->withErrors(['identifier' => 'حساب شما غیرفعال شده است.']);
        }

        // ثبت آخرین فعالیت
        $request->session()->put('expert_last_activity', now()->timestamp);

        return $next($request);
    }
}
