<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * اجبار می‌کند که کاربر از مسیر /tariff-officer/login آمده باشد.
 * اگر مستقیم روی /admin/tariffs برود (با هر نقشی)، به فرم ورود کارشناسان ریدایرکت می‌شود.
 */
class RequireTariffOfficerMode
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('tariff.officer.login');
        }

        if ($request->session()->get('tariff_officer_mode') !== true) {
            return redirect()->route('tariff.officer.login')
                ->withErrors(['identifier' => 'برای ورود به پنل کارشناسان، ابتدا از این صفحه وارد شوید.']);
        }

        return $next($request);
    }
}
