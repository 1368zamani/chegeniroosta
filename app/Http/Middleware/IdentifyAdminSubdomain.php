<?php

namespace App\Http\Middleware;

use App\Models\Section;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyAdminSubdomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = config('app.base_domain', 'chegeniroosta.ir');
        $adminPrefix = 'admin.';

        // چک میکنه اگر admin.{subdomain}.chegeniroosta.ir هست
        if (!str_starts_with($host, $adminPrefix)) {
            return $next($request);
        }

        $withoutAdmin = substr($host, strlen($adminPrefix));

        if (!str_ends_with($withoutAdmin, '.' . $baseDomain)) {
            return $next($request);
        }

        $subdomain = substr($withoutAdmin, 0, -strlen('.' . $baseDomain));

        if ($subdomain === '') {
            return $next($request);
        }

        $section = Section::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();

        if ($section) {
            app()->instance('currentSection', $section);
            view()->share('currentSection', $section);

            // برای ادمین: چک میکنه که کاربر دسترسی داره
            if (auth()->check() && !auth()->user()->canManageSection($section->id)) {
                abort(403, 'شما به این بخش دسترسی ندارید.');
            }
        }

        return $next($request);
    }
}
