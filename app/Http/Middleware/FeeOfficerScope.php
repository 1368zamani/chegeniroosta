<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * کارشناس مالی/عمرانی فقط به /admin/fees/* دسترسی دارد.
 * اگر بخواهد به /admin یا جای دیگری برود، به داشبورد fees ریدایرکت میشود.
 */
class FeeOfficerScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isFeeOfficer()) {
            // مسیرهای مجاز برای fee_officer
            $allowed = [
                'admin/fees',
                'admin/fees/*',
                'fees-logout',
                'logout',
            ];

            $path = trim($request->path(), '/');

            $isAllowed = false;
            foreach ($allowed as $pattern) {
                if ($path === $pattern || fnmatch($pattern, $path)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed && !$request->isMethod('POST')) {
                // اجازه دادن به درخواست‌های POST برای approve/reject
                if (!str_starts_with($path, 'admin/fees/')) {
                    return redirect()->route('admin.fees.dashboard')
                        ->with('info', 'شما فقط به پنل عوارض دسترسی دارید.');
                }
            }
        }

        return $next($request);
    }
}
