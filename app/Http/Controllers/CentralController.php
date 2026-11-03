<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionNews;

class CentralController extends Controller
{
    /**
     * صفحه‌ی اصلی پرتال مرکزی (chegeniroosta.ir)
     *
     * علاوه بر فهرست بخش‌ها، خبرهای **همه‌ی بخش‌ها** را هم نمایش می‌دهد:
     * پشت سر هم و مرتب بر اساس تاریخ انتشار (جدیدترین اول).
     */
    public function home()
    {
        $sections = Section::where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
         * خبرهای همه‌ی بخش‌ها — یک فهرست واحد.
         *
         * مرتب‌سازی: تاریخ انتشار نزولی (جدیدترین اول).
         * خبرهای «سنجاق‌شده» هم در جای خودشان بر اساس تاریخ می‌آیند
         * تا ترتیب کاملاً بر اساس تاریخ باشد — همان چیزی که خواسته شد.
         */
        $news = SectionNews::query()
            ->with(['section:id,name,slug,subdomain,is_active', 'stat'])
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->whereNotNull('section_id')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        // آیا خبر بیشتری از سقف نمایش وجود دارد؟
        $newsTotal = SectionNews::query()
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->whereNotNull('section_id')
            ->count();

        return view('central.home', [
            'sections'  => $sections,
            'news'      => $news,
            'newsTotal' => $newsTotal,
        ]);
    }
}
