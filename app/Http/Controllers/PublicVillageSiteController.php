<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\VillageNews;
use App\Models\VillageImage;
use App\Models\VillageProject;
use App\Models\VillageRequest;
use App\Models\VillageEvent;
use App\Models\VillageFacility;
use App\Models\VillageCouncilMember;
use App\Models\VillageAttraction;
use Illuminate\Http\Request;

class PublicVillageSiteController extends Controller
{
    public function home($slug)
    {
        $village = $this->getVillage($slug);

        $stats = [
            'last_news'         => optional(VillageNews::where('village_id', $village->id)->where('is_published', true)->orderBy('created_at', 'desc')->first())->created_at,
            'active_projects'   => VillageProject::where('village_id', $village->id)->where('is_active', true)->whereIn('status', ['planned', 'in_progress'])->count(),
            'tracking_requests' => VillageRequest::where('village_id', $village->id)->whereIn('status', ['submitted', 'reviewing', 'assigned', 'processing'])->count(),
            'new_announcements' => VillageNews::where('village_id', $village->id)->where('is_published', true)->where('is_pinned', true)->count(),
        ];

        $latestNews = VillageNews::where('village_id', $village->id)
            ->where('is_published', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        $upcomingEvents = VillageEvent::where('village_id', $village->id)
            ->where('is_active', true)
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $projects = VillageProject::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $facilities = VillageFacility::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(12)
            ->get();

        return view('village.site.home', compact('village', 'stats', 'latestNews', 'upcomingEvents', 'projects', 'facilities'));
    }

    public function newsList($slug)
    {
        $village = $this->getVillage($slug);

        $news = VillageNews::where('village_id', $village->id)
            ->with('stat')
            ->where('is_published', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return view('village.site.news', compact('village', 'news'));
    }

    public function newsShow($slug, $newsSlug)
    {
        $village = $this->getVillage($slug);

        $news = VillageNews::where('village_id', $village->id)
            ->with('stat')
            ->where('slug', $newsSlug)
            ->where('is_published', true)
            ->firstOrFail();

        $related = VillageNews::where('village_id', $village->id)
            ->where('is_published', true)
            ->where('id', '!=', $news->id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        // ═══ شمارنده‌ی بازدید (هر نشست یک بار) ═══
        $news->recordView();

        return view('village.site.news-show', compact('village', 'news', 'related'));
    }

    public function projects($slug)
    {
        $village = $this->getVillage($slug);

        $projects = VillageProject::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('status')
            ->orderBy('sort_order')
            ->get();

        return view('village.site.projects', compact('village', 'projects'));
    }

    public function about($slug)
    {
        $village = $this->getVillage($slug);

        $attractions = VillageAttraction::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $gallery = VillageImage::where('village_id', $village->id)
            ->orderBy('id')
            ->limit(20)
            ->get();

        return view('village.site.about', compact('village', 'attractions', 'gallery'));
    }

    public function dehyar($slug)
    {
        $village = $this->getVillage($slug);

        return view('village.site.dehyar', compact('village'));
    }

    public function council($slug)
    {
        $village = $this->getVillage($slug);

        $members = VillageCouncilMember::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('village.site.council', compact('village', 'members'));
    }

    public function resolutions($slug)
    {
        $village = $this->getVillage($slug);

        $resolutions = \App\Models\VillageCouncilResolution::where('village_id', $village->id)
            ->with('stat')
            ->where('is_public', true)
            ->orderBy('session_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('village.site.resolutions', compact('village', 'resolutions'));
    }

    public function facilities($slug)
    {
        $village = $this->getVillage($slug);

        $facilities = VillageFacility::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        return view('village.site.facilities', compact('village', 'facilities'));
    }

    public function events($slug)
    {
        $village = $this->getVillage($slug);

        $upcoming = VillageEvent::where('village_id', $village->id)
            ->where('is_active', true)
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->get();

        $past = VillageEvent::where('village_id', $village->id)
            ->where('is_active', true)
            ->where('event_date', '<', now()->toDateString())
            ->orderBy('event_date', 'desc')
            ->limit(20)
            ->get();

        return view('village.site.events', compact('village', 'upcoming', 'past'));
    }

    public function requestCreate($slug)
    {
        $village = $this->getVillage($slug);

        $subjects = [
            'آب', 'برق', 'گاز', 'مخابرات', 'راه',
            'پسماند', 'روشنایی', 'عمران', 'دهیاری', 'سایر'
        ];

        // فایل‌های قابل دانلود — در پایین همین صفحه نمایش داده می‌شوند
        $downloads = \App\Models\VillageDownload::forVillage($village->id)
            ->active()
            ->ordered()
            ->get();

        return view('village.site.request-create', compact('village', 'subjects', 'downloads'));
    }

    /**
     * دانلود فایل روستا + شمارش تعداد دانلود.
     * GET /village/{slug}/download/{id}
     */
    public function downloadFile($slug, $id)
    {
        $village = $this->getVillage($slug);

        $download = \App\Models\VillageDownload::where('village_id', $village->id)
            ->active()
            ->findOrFail($id);

        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        if (! $download->file_path || ! $disk->exists($download->file_path)) {
            abort(404, 'فایل مورد نظر یافت نشد.');
        }

        // شمارش اتمی دانلود — بدون قفل کردن ردیف
        \App\Models\VillageDownload::whereKey($download->id)->increment('download_count');

        return $disk->download($download->file_path, $download->file_name ?: null);
    }

    // ══════════════════════════════════════════════════════
    // محصولات روستا (فروش)
    // ══════════════════════════════════════════════════════

    /**
     * فهرست محصولات روستا.
     * GET /village/{slug}/products
     */
    public function products(Request $request, $slug)
    {
        $village = $this->getVillage($slug);

        $query = \App\Models\VillageProduct::with(['images'])
            ->forVillage($village->id)
            ->active();

        // فیلتر دسته‌بندی
        $category = trim((string) $request->query('category'));
        if ($category !== '') {
            $query->where('category', $category);
        }

        // جست‌وجو
        $search = trim((string) $request->query('q'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->ordered()->paginate(12)->withQueryString();

        // دسته‌های موجود (فقط آن‌هایی که محصول دارند)
        $categories = \App\Models\VillageProduct::forVillage($village->id)
            ->active()
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('village.site.products', compact('village', 'products', 'categories', 'category', 'search'));
    }

    /**
     * صفحه‌ی جزئیات محصول.
     * GET /village/{slug}/products/{productSlug}
     */
    public function productShow($slug, $productSlug)
    {
        $village = $this->getVillage($slug);

        $product = \App\Models\VillageProduct::with(['images', 'files'])
            ->forVillage($village->id)
            ->active()
            ->where('slug', $productSlug)
            ->firstOrFail();

        // شمارش بازدید (اتمی)
        \App\Models\VillageProduct::whereKey($product->id)->increment('views');

        // محصولات دیگر همین روستا
        $more = \App\Models\VillageProduct::with('images')
            ->forVillage($village->id)
            ->active()
            ->where('id', '!=', $product->id)
            ->ordered()
            ->limit(4)
            ->get();

        return view('village.site.product-show', compact('village', 'product', 'more'));
    }

    /**
     * دانلود فایل ضمیمه‌ی محصول + شمارش.
     * GET /village/{slug}/product-file/{id}
     */
    public function productFile($slug, $id)
    {
        $village = $this->getVillage($slug);

        $file = \App\Models\VillageProductFile::whereHas('product', function ($q) use ($village) {
                $q->where('village_id', $village->id)->where('is_active', true);
            })
            ->findOrFail($id);

        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        if (! $file->file_path || ! $disk->exists($file->file_path)) {
            abort(404, 'فایل مورد نظر یافت نشد.');
        }

        \App\Models\VillageProductFile::whereKey($file->id)->increment('download_count');

        return $disk->download($file->file_path, $file->file_name ?: null);
    }

    /**
     * صفحه‌ی مرکز دانلود روستا (همه‌ی فایل‌های عمومی).
     * GET /village/{slug}/downloads
     */
    public function downloads($slug)
    {
        $village = $this->getVillage($slug);

        $downloads = \App\Models\VillageDownload::forVillage($village->id)
            ->active()
            ->ordered()
            ->get();

        return view('village.site.downloads', compact('village', 'downloads'));
    }

    public function requestStore(Request $request, $slug)
    {
        $village = $this->getVillage($slug);

        $data = $request->validate([
            'full_name'   => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
            'subject'     => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'attachment'  => 'nullable|file|max:5120',
        ]);

        $data['village_id'] = $village->id;
        $data['tracking_code'] = VillageRequest::generateTrackingCode();
        $data['status'] = 'submitted';
        $data['ip_address'] = $request->ip();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('village-requests/' . $village->id, 'public');
        }

        $newRequest = VillageRequest::create($data);

        return redirect()
            ->route('village.site.request.success', ['slug' => $slug, 'code' => $newRequest->tracking_code]);
    }

    public function requestSuccess($slug, $code)
    {
        $village = $this->getVillage($slug);

        $request = VillageRequest::where('village_id', $village->id)
            ->where('tracking_code', $code)
            ->firstOrFail();

        return view('village.site.request-success', compact('village', 'request'));
    }

    public function requestTrack($slug)
    {
        $village = $this->getVillage($slug);
        return view('village.site.request-track', compact('village'));
    }

    public function requestTrackResult(Request $request, $slug)
    {
        $village = $this->getVillage($slug);

        $request->validate(['tracking_code' => 'required|string']);

        $found = VillageRequest::where('village_id', $village->id)
            ->where('tracking_code', $request->tracking_code)
            ->first();

        return view('village.site.request-track-result', compact('village', 'found'));
    }

    public function attractions($slug)
    {
        $village = $this->getVillage($slug);

        $attractions = VillageAttraction::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('village.site.attractions', compact('village', 'attractions'));
    }

    public function gallery($slug)
    {
        $village = $this->getVillage($slug);

        $images = VillageImage::where('village_id', $village->id)
            ->orderBy('id')
            ->get();

        return view('village.site.gallery', compact('village', 'images'));
    }

    private function getVillage($slug): Village
    {
        return Village::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }
}
