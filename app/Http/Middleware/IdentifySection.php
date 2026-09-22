<?php

namespace App\Http\Middleware;

use App\Models\Section;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifySection
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = config('app.base_domain', 'chegeniroosta.ir');

        if (!str_ends_with($host, '.' . $baseDomain)) {
            return $next($request);
        }

        $subdomain = substr($host, 0, -strlen('.' . $baseDomain));

        if (
            $subdomain === '' ||
            $subdomain === 'www' ||
            $subdomain === 'admin'
        ) {
            return $next($request);
        }

        $section = Section::where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();

        if (!$section) {
            return $next($request);
        }

        app()->instance('currentSection', $section);
        view()->share('currentSection', $section);

        return $next($request);
    }
}
