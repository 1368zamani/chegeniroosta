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

    /** sitemap اخبار برای Google News */
    public function sitemapNews()
    {
        $news = \App\Models\SectionNews::orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(1000)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        foreach ($news as $n) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars(url('/news/' . $n->slug)) . "</loc>\n";
            $xml .= "    <news:news>\n";
            $xml .= "      <news:publication>\n";
            $xml .= "        <news:name>پایگاه اطلاع‌رسانی چگنی</news:name>\n";
            $xml .= "        <news:language>fa</news:language>\n";
            $xml .= "      </news:publication>\n";
            $date = $n->published_at ?? $n->created_at;
            $xml .= "      <news:publication_date>" . $date->toAtomString() . "</news:publication_date>\n";
            $xml .= "      <news:title>" . htmlspecialchars($n->title) . "</news:title>\n";
            $xml .= "    </news:news>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'application/xml');
    }


    /**
     * Sitemap اختصاصی هر روستا
     */
    public function villageSitemap(string $slug)
    {
        $village = \App\Models\Village::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $urls = [];
        $base = url('/village/' . $village->slug);

        $urls[] = ['loc' => $base, 'priority' => '1.0', 'changefreq' => 'daily'];
        $urls[] = ['loc' => $base . '/about', 'priority' => '0.8', 'changefreq' => 'weekly'];
        $urls[] = ['loc' => $base . '/news', 'priority' => '0.8', 'changefreq' => 'daily'];
        $urls[] = ['loc' => $base . '/council', 'priority' => '0.7', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $base . '/dehyar', 'priority' => '0.7', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $base . '/projects', 'priority' => '0.6', 'changefreq' => 'weekly'];
        $urls[] = ['loc' => $base . '/events', 'priority' => '0.6', 'changefreq' => 'weekly'];
        $urls[] = ['loc' => $base . '/facilities', 'priority' => '0.5', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $base . '/attractions', 'priority' => '0.6', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $base . '/gallery', 'priority' => '0.5', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $base . '/resolutions', 'priority' => '0.5', 'changefreq' => 'monthly'];

        // اخبار روستا
        $news = \App\Models\VillageNews::where('village_id', $village->id)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(100)
            ->get();

        foreach ($news as $n) {
            $urls[] = [
                'loc' => $base . '/news/' . $n->slug,
                'lastmod' => optional($n->updated_at)->toAtomString(),
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $u) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($u['loc']) . '</loc>' . PHP_EOL;
            if (!empty($u['lastmod'])) {
                $xml .= '    <lastmod>' . $u['lastmod'] . '</lastmod>' . PHP_EOL;
            }
            if (!empty($u['changefreq'])) {
                $xml .= '    <changefreq>' . $u['changefreq'] . '</changefreq>' . PHP_EOL;
            }
            if (!empty($u['priority'])) {
                $xml .= '    <priority>' . $u['priority'] . '</priority>' . PHP_EOL;
            }
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * RSS اخبار هر روستا
     */
    public function villageRss(string $slug)
    {
        $village = \App\Models\Village::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $news = \App\Models\VillageNews::where('village_id', $village->id)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();

        $base = url('/village/' . $village->slug);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . PHP_EOL;
        $xml .= '  <channel>' . PHP_EOL;
        $xml .= '    <title>اخبار روستای ' . htmlspecialchars($village->name) . '</title>' . PHP_EOL;
        $xml .= '    <link>' . htmlspecialchars($base) . '</link>' . PHP_EOL;
        $xml .= '    <description>آخرین اخبار و اطلاعیه‌های روستای ' . htmlspecialchars($village->name) . '</description>' . PHP_EOL;
        $xml .= '    <language>fa-IR</language>' . PHP_EOL;
        $xml .= '    <atom:link href="' . htmlspecialchars($base . '/feed.xml') . '" rel="self" type="application/rss+xml" />' . PHP_EOL;

        foreach ($news as $n) {
            $link = $base . '/news/' . $n->slug;
            $pubDate = optional($n->published_at ?? $n->created_at)->toRfc2822String();

            $xml .= '    <item>' . PHP_EOL;
            $xml .= '      <title>' . htmlspecialchars($n->title ?? '') . '</title>' . PHP_EOL;
            $xml .= '      <link>' . htmlspecialchars($link) . '</link>' . PHP_EOL;
            $xml .= '      <guid isPermaLink="true">' . htmlspecialchars($link) . '</guid>' . PHP_EOL;
            if ($pubDate) {
                $xml .= '      <pubDate>' . $pubDate . '</pubDate>' . PHP_EOL;
            }
            $desc = strip_tags($n->summary ?? $n->content ?? '');
            $desc = mb_substr($desc, 0, 300);
            $xml .= '      <description>' . htmlspecialchars($desc) . '</description>' . PHP_EOL;
            $xml .= '    </item>' . PHP_EOL;
        }

        $xml .= '  </channel>' . PHP_EOL;
        $xml .= '</rss>';

        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    /**
     * Sitemap ایندکس همه روستاها
     */
    public function villagesIndex()
    {
        $villages = \App\Models\Village::where('is_active', true)
            ->orderBy('id')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($villages as $v) {
            $xml .= '  <sitemap>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars(url('/village/' . $v->slug . '/sitemap.xml')) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . optional($v->updated_at)->toAtomString() . '</lastmod>' . PHP_EOL;
            $xml .= '  </sitemap>' . PHP_EOL;
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
    }

}
