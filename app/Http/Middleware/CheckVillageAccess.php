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

        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // پارامتر route ممکن است مدل Village باشد یا رشته (بسته به route binding)
        $village = $request->route('village');
        if (is_string($village) || is_numeric($village)) {
            $village = Village::find($village);
        }

        if (
            $user->role !== 'village_admin' ||
            !$user->village_id ||
            !$village ||
            (int) $user->village_id !== (int) $village->id
        ) {
            abort(403, 'شما به این روستا دسترسی ندارید.');
        }

        return $next($request);
    }
}
