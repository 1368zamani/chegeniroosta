<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * مدیریت فایل‌های قابل دانلود هر روستا.
 *
 * این فایل‌ها در پایین صفحه‌ی «ثبت درخواست» شهروند نمایش داده می‌شوند.
 */
class DownloadController extends Controller
{
    private const DISK = 'public';
    private const DIR  = 'village-downloads';

    /** حداکثر حجم هر فایل: ۱۲ مگابایت */
    private const MAX_KB = 12288;

    // ══════════════════════════════════════════════════════
    // فهرست
    // ══════════════════════════════════════════════════════

    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $downloads = VillageDownload::forVillage($village->id)
            ->ordered()
            ->paginate(30);

        return view('admin.village.downloads.index', compact('village', 'downloads'));
    }

    // ══════════════════════════════════════════════════════
    // افزودن
    // ══════════════════════════════════════════════════════

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);

        return view('admin.village.downloads.form', [
            'village'   => $village,
            'download'  => new VillageDownload(),
            'maxKb'     => self::MAX_KB,
        ]);
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'file'        => ['required', 'file', 'max:' . self::MAX_KB],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'title.required' => 'عنوان فایل را وارد کنید.',
            'file.required'  => 'یک فایل انتخاب کنید.',
            'file.max'       => 'حجم فایل حداکثر باید ۱۲ مگابایت باشد.',
        ]);

        $file = $request->file('file');
        $path = $file->store(self::DIR, self::DISK);

        VillageDownload::create([
            'village_id'  => $village->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => (int) $file->getSize(),
            'mime_type'   => $file->getClientMimeType(),
            'is_active'   => (bool) ($data['is_active'] ?? true),
            'sort_order'  => (int) ($data['sort_order'] ?? 0),
        ]);

        return redirect()
            ->route('admin.village.downloads.index', $village->id)
            ->with('success', 'فایل با موفقیت بارگذاری شد.');
    }

    // ══════════════════════════════════════════════════════
    // ویرایش
    // ══════════════════════════════════════════════════════

    public function edit($villageId, $id)
    {
        $village  = $this->authorizeVillage($villageId);
        $download = $this->findInVillage($village, $id);

        return view('admin.village.downloads.form', [
            'village'  => $village,
            'download' => $download,
            'maxKb'    => self::MAX_KB,
        ]);
    }

    public function update(Request $request, $villageId, $id)
    {
        $village  = $this->authorizeVillage($villageId);
        $download = $this->findInVillage($village, $id);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'file'        => ['nullable', 'file', 'max:' . self::MAX_KB],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'title.required' => 'عنوان فایل را وارد کنید.',
            'file.max'       => 'حجم فایل حداکثر باید ۱۲ مگابایت باشد.',
        ]);

        $payload = [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'is_active'   => (bool) ($data['is_active'] ?? false),
            'sort_order'  => (int) ($data['sort_order'] ?? 0),
        ];

        // اگر فایل جدید انتخاب شد، فایل قبلی پاک می‌شود
        if ($request->hasFile('file')) {
            if ($download->file_path && Storage::disk(self::DISK)->exists($download->file_path)) {
                Storage::disk(self::DISK)->delete($download->file_path);
            }

            $file = $request->file('file');
            $payload['file_path'] = $file->store(self::DIR, self::DISK);
            $payload['file_name'] = $file->getClientOriginalName();
            $payload['file_size'] = (int) $file->getSize();
            $payload['mime_type'] = $file->getClientMimeType();
        }

        $download->update($payload);

        return redirect()
            ->route('admin.village.downloads.index', $village->id)
            ->with('success', 'تغییرات ذخیره شد.');
    }

    // ══════════════════════════════════════════════════════
    // حذف
    // ══════════════════════════════════════════════════════

    public function destroy($villageId, $id)
    {
        $village  = $this->authorizeVillage($villageId);
        $download = $this->findInVillage($village, $id);

        if ($download->file_path && Storage::disk(self::DISK)->exists($download->file_path)) {
            Storage::disk(self::DISK)->delete($download->file_path);
        }

        $download->delete();

        return redirect()
            ->route('admin.village.downloads.index', $village->id)
            ->with('success', 'فایل حذف شد.');
    }

    // ══════════════════════════════════════════════════════
    // کمکی‌ها
    // ══════════════════════════════════════════════════════

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);

        if (! auth()->user()->canManageVillage($village)) {
            abort(403, 'شما به این روستا دسترسی ندارید.');
        }

        return $village;
    }

    private function findInVillage(Village $village, $id): VillageDownload
    {
        return VillageDownload::where('village_id', $village->id)->findOrFail($id);
    }
}
