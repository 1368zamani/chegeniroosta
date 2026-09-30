<?php

namespace App\Http\Middleware;

use App\Models\Village;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckVillageAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // سوپرادمین: دسترسی کامل
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // پارامتر route ممکن است مدل Village باشد یا رشته
        $village = $request->route('village');
        if (is_string($village) || is_numeric($village)) {
            $village = Village::find($village);
        }

        if (!$village) {
            abort(404, 'روستا یافت نشد.');
        }

        // بخشدار: اگر روستا در بخش او باشد، دسترسی دارد
        if ($user->isSectionAdmin()) {
            if ((int) $user->section_id === (int) $village->section_id) {
                return $next($request);
            }
            abort(403, 'این روستا در بخش شما نیست.');
        }

        // دهیار: فقط روستای خودش
        if ($user->isVillageAdmin()) {
            if ($user->village_id && (int) $user->village_id === (int) $village->id) {
                return $next($request);
            }
            abort(403, 'شما به این روستا دسترسی ندارید.');
        }

        abort(403, 'شما به این روستا دسترسی ندارید.');
    }
}
