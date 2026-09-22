<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // فقط سوپر ادمین
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'فقط سوپر ادمین می‌تواند تنظیمات سایت را ویرایش کند.');
        }

        // مقادیر پیش‌فرض اگه تنظیم نشده باشن
        $defaults = [
            'header_title'       => 'پایگاه اطلاع‌رسانی بخش‌های شهرستان چگنی',
            'header_subtitle'    => 'وزارت کشور • استانداری لرستان • فرمانداری چگنی',
            'header_emblem'      => 'images/gov/emblem.png',
            'banner_text'        => 'دولت چهاردهم، دولت وفاق ملی',
            'banner_highlight_1' => 'دولت چهاردهم',
            'banner_highlight_2' => 'دولت وفاق ملی',
            'footer_about'       => 'سامانه اطلاع‌رسانی روستاهای شهرستان چگنی، استان لرستان.',
            'footer_copyright'   => '© ' . date('Y') . ' — سامانه اطلاع‌رسانی روستاهای چگنی | تمامی حقوق محفوظ است',
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = SiteSetting::get($key, $default);
        }

        // اشعار
        $verses = SiteSetting::get('poetry_verses', null);
        if (!$verses) {
            $verses = $this->defaultVerses();
        }
        $settings['poetry_verses'] = is_array($verses) ? $verses : $this->defaultVerses();

        // لینک‌های فوتر
        $links = SiteSetting::get('footer_links', null);
        if (!$links) {
            $links = $this->defaultFooterLinks();
        }
        $settings['footer_links'] = is_array($links) ? $links : $this->defaultFooterLinks();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $group = $request->input('_group', 'general');

        // ---- تنظیمات متنی ----
        $textKeys = [
            'header_title', 'header_subtitle',
            'banner_text', 'banner_highlight_1', 'banner_highlight_2',
            'footer_about', 'footer_copyright',
        ];

        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->input($key), $this->groupOf($key), 'text');
            }
        }

        // ---- آرم هدر (عکس) ----
        if ($request->hasFile('header_emblem_file')) {
            $request->validate(['header_emblem_file' => 'image|max:2048']);
            $path = $request->file('header_emblem_file')->store('settings', 'public');
            SiteSetting::set('header_emblem', $path, 'header', 'image');
        }

        // ---- اشعار ----
        if ($request->has('poetry_verses')) {
            $versesInput = $request->input('poetry_verses');

            // فرمت: متن|||شاعر در هر خط
            $verses = [];
            if (is_string($versesInput) && trim($versesInput) !== '') {
                foreach (explode("\n", $versesInput) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $parts = array_map('trim', explode('|||', $line));
                    if (count($parts) >= 2) {
                        $verses[] = ['text' => $parts[0], 'poet' => $parts[1]];
                    }
                }
            }
            SiteSetting::set('poetry_verses', $verses, 'poetry', 'json');
        }

        // ---- لینک‌های فوتر ----
        if ($request->has('footer_links')) {
            $linksInput = $request->input('footer_links');

            // فرمت: عنوان|||آدرس در هر خط
            $links = [];
            if (is_string($linksInput) && trim($linksInput) !== '') {
                foreach (explode("\n", $linksInput) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $parts = array_map('trim', explode('|||', $line));
                    if (count($parts) >= 2) {
                        $links[] = ['title' => $parts[0], 'url' => $parts[1]];
                    }
                }
            }
            SiteSetting::set('footer_links', $links, 'footer', 'json');
        }

        SiteSetting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات با موفقیت ذخیره شد.');
    }

    private function groupOf(string $key): string
    {
        if (str_starts_with($key, 'header_')) return 'header';
        if (str_starts_with($key, 'footer_')) return 'footer';
        if (str_starts_with($key, 'banner_')) return 'banner';
        return 'general';
    }

    private function defaultVerses(): array
    {
        return [
            ['text' => 'بنی آدم اعضای یک پیکرند / که در آفرینش ز یک گوهرند', 'poet' => 'سعدی'],
            ['text' => 'چو عضوی به درد آورد روزگار / دگر عضوها را نماند قرار', 'poet' => 'سعدی'],
            ['text' => 'توانا بود هر که دانا بود / ز دانش دل پیر برنا بود', 'poet' => 'فردوسی'],
            ['text' => 'دوش دیدم که ملائک در میخانه زدند / گِل آدم بسرشتند و به پیمانه زدند', 'poet' => 'حافظ'],
        ];
    }

    private function defaultFooterLinks(): array
    {
        return [
            ['title' => 'وزارت کشور', 'url' => 'https://www.moi.ir'],
            ['title' => 'استانداری لرستان', 'url' => 'https://lorestan.ostan-lr.ir'],
            ['title' => 'نقشه سایت', 'url' => '/sitemap.xml'],
            ['title' => 'خوراک خبری RSS', 'url' => '/feed.xml'],
        ];
    }
}
