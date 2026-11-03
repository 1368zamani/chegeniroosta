<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\VillageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminVillageImageController extends Controller
{
    public function index($villageId)
    {
        $village = Village::findOrFail($villageId);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }

        $images = VillageImage::where('village_id', $village->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.images.index', compact('village', 'images'));
    }

    public function create($villageId)
    {
        $village = Village::findOrFail($villageId);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }

        return view('admin.images.create', compact('village'));
    }

    public function store(Request $request, $villageId)
    {
        $village = Village::findOrFail($villageId);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }

        $request->validate([
            'images'   => 'required|array',
            'images.*' => 'image|max:5120',
            'caption'  => 'nullable|string|max:255',
        ]);

        $count = 0;
        foreach ($request->file('images') as $file) {
            $path = $file->store('village-images/' . $village->id, 'public');

            VillageImage::create([
                'village_id'  => $village->id,
                'image'       => $path,
                'title'       => $request->input('caption') ?: $file->getClientOriginalName(),
                'description' => null,
                'is_cover'    => false,
            ]);

            $count++;
        }

        return redirect()
            ->route('admin.village.images.index', $village->id)
            ->with('success', "{$count} تصویر با موفقیت آپلود شد.");
    }

    public function destroy($villageId, $imageId)
    {
        $village = Village::findOrFail($villageId);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }

        /*
         * ⚠️ امنیت (IDOR): قبلاً `VillageImage::findOrFail($imageId)` بدون
         * محدودیت روستا بود. یعنی ادمین روستای «الف» می‌توانست با فرستادن
         * شناسه‌ی تصویر روستای «ب»، فایل و رکورد آن روستا را حذف کند.
         * الان تصویر باید متعلق به همین روستا باشد.
         */
        $image = VillageImage::where('village_id', $village->id)->findOrFail($imageId);

        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return redirect()
            ->route('admin.village.images.index', $village->id)
            ->with('success', 'تصویر حذف شد.');
    }

    public function setCover($villageId, $imageId)
    {
        $village = Village::findOrFail($villageId);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }

        // ⚠️ امنیت (IDOR): تصویر باید متعلق به همین روستا باشد
        $image = VillageImage::where('village_id', $village->id)->findOrFail($imageId);

        // پاک کردن is_cover قبلی
        VillageImage::where('village_id', $village->id)
            ->update(['is_cover' => false]);

        // تنظیم عکس جدید
        $image->is_cover = true;
        $image->save();

        // تنظیم در Village
        $village->cover_image = $image->image;
        $village->save();

        return redirect()
            ->route('admin.village.images.index', $village->id)
            ->with('success', 'تصویر به عنوان کاور روستا تنظیم شد.');
    }
}
