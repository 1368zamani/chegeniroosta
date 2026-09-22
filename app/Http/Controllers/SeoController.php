<?php

namespace App\Http\Controllers;

use App\Models\Village;

class SeoController extends Controller
{
    public function sitemap()
    {
        $villages = Village::where('is_active', true)
            ->whereNotNull('subdomain')
            ->get();

        return response()
            ->view('seo.sitemap', compact('villages'))
            ->header('Content-Type', 'application/xml');
    }
}
