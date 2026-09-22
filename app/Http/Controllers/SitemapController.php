<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Village;
use App\Models\SectionNews;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function sitemap()
    {
        $baseDomain = config('app.base_domain', 'chegeniroosta.ir');
        $scheme = request()->getScheme() . '://';

        $urls = [];

        // صفحه اصلی
        $urls[] = [
            'loc' => $scheme . $baseDomain . '/',
            'priority' => '1.0',
            'changefreq' => 'daily',
        ];

        // بخش‌ها
        $sections = Section::where('is_active', true)->get();
        foreach ($sections as $section) {
            $sub = $section->subdomain . '.' . $baseDomain;
            $urls[] = ['loc' => $scheme . $sub . '/', 'priority' => '0.9', 'changefreq' => 'daily'];
            $urls[] = ['loc' => $scheme . $sub . '/news', 'priority' => '0.8', 'changefreq' => 'daily'];
            if ($section->bakhshdar_name) {
                $urls[] = ['loc' => $scheme . $sub . '/bakhshdar', 'priority' => '0.6', 'changefreq' => 'monthly'];
            }
        }

        // روستاها
        $villages = Village::where('is_active', true)->get();
        foreach ($villages as $village) {
            $urls[] = [
                'loc' => $scheme . $baseDomain . '/village/' . $village->slug,
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ];
        }

        // اخبار بخش‌ها
        $news = SectionNews::where('is_published', true)->get();
        foreach ($news as $item) {
            $section = Section::find($item->section_id);
            if ($section && $section->subdomain) {
                $sub = $section->subdomain . '.' . $baseDomain;
                $urls[] = [
                    'loc' => $scheme . $sub . '/news/' . $item->slug,
                    'priority' => '0.6',
                    'changefreq' => 'monthly',
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function rss()
    {
        $baseDomain = config('app.base_domain', 'chegeniroosta.ir');
        $scheme = request()->getScheme() . '://';

        $news = SectionNews::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= "  <channel>\n";
        $xml .= "    <title>اخبار بخشداری‌های شهرستان چگنی</title>\n";
        $xml .= "    <link>" . $scheme . $baseDomain . "</link>\n";
        $xml .= "    <description>آخرین اخبار و اطلاعیه‌های بخش‌های شهرستان چگنی</description>\n";
        $xml .= "    <language>fa-IR</language>\n";
        $xml .= '    <atom:link href="' . $scheme . $baseDomain . '/feed.xml" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($news as $item) {
            $section = Section::find($item->section_id);
            $sub = $section ? ($section->subdomain . '.' . $baseDomain) : $baseDomain;
            $link = $scheme . $sub . '/news/' . $item->slug;

            $pubDate = $item->published_at ? $item->published_at->toRfc2822String() : $item->created_at->toRfc2822String();
            $desc = strip_tags($item->excerpt ?: $item->content);

            $xml .= "    <item>\n";
            $xml .= "      <title><![CDATA[" . $item->title . "]]></title>\n";
            $xml .= "      <link>" . htmlspecialchars($link) . "</link>\n";
            $xml .= "      <guid>" . htmlspecialchars($link) . "</guid>\n";
            $xml .= "      <pubDate>" . $pubDate . "</pubDate>\n";
            $xml .= "      <description><![CDATA[" . mb_substr($desc, 0, 300) . "]]></description>\n";
            $xml .= "    </item>\n";
        }

        $xml .= "  </channel>\n";
        $xml .= '</rss>';

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']);
    }
}
