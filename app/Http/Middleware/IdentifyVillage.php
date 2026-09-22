<?php

namespace App\Http\Middleware;

use App\Models\Section;
use App\Models\Village;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyVillage
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = config('app.base_domain', 'localhost');

        if (!str_ends_with($host, '.' . $baseDomain)) {
            return $next($request);
        }

        $subdomain = substr($host, 0, -strlen('.' . $baseDomain));

        if ($subdomain === '' || $subdomain === 'www' || $subdomain === 'admin') {
            return $next($request);
        }

        // اگه این subdomain قبلاً به عنوان Section شناسایی شده، کاری نکن
        if (app()->bound('currentSection')) {
            return $next($request);
        }

        // اگه subdomain مربوط به یه Section هست، اصلاً دنبال روستا نگرد
        $isSectionSubdomain = Section::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->exists();

        if ($isSectionSubdomain) {
            return $next($request);
        }

        $village = Village::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();

        if (!$village) {
            // به جای 404، اجازه بده درخواست ادامه پیدا کنه
            // تا Dispatcher بتونه تصمیم بگیره
            return $next($request);
        }

        app()->instance('currentVillage', $village);
        view()->share('currentVillage', $village);

        return $next($request);
    }
}
