<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DehyariPhotoController extends Controller
{
    private function authorizeVillage(Village $village): void
    {
        $u = auth()->user();
        if (!$u) abort(403);

        if ($u->isSuperAdmin()) return;

        if ($u->isSectionAdmin() && $village->section_id === $u->section_id) return;

        if ($u->isVillageAdmin() && $u->village_id === $village->id) return;

        abort(403, 'دسترسی ندارید.');
    }

    public function upload(Request $request, Village $village)
    {
        $this->authorizeVillage($village);

        $request->validate([
            'dehyar_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'dehyar_photo.required' => 'انتخاب عکس الزامی است.',
            'dehyar_photo.image'    => 'فایل باید تصویر باشد.',
            'dehyar_photo.max'      => 'حجم عکس نباید بیشتر از ۵ مگابایت باشد.',
        ]);

        if ($village->dehyar_photo && Storage::disk('public')->exists($village->dehyar_photo)) {
            Storage::disk('public')->delete($village->dehyar_photo);
        }

        $path = $request->file('dehyar_photo')->store('dehyar-photos', 'public');
        $village->update(['dehyar_photo' => $path]);

        return back()->with('success', '✅ عکس دهیار آپلود شد.');
    }

    public function delete(Village $village)
    {
        $this->authorizeVillage($village);

        if ($village->dehyar_photo && Storage::disk('public')->exists($village->dehyar_photo)) {
            Storage::disk('public')->delete($village->dehyar_photo);
        }

        $village->update(['dehyar_photo' => null]);

        return back()->with('success', '✅ عکس دهیار حذف شد.');
    }

    public function update(Request $request, Village $village)
    {
        $this->authorizeVillage($village);

        $data = $request->validate([
            'dehyar_name'      => 'required|string|max:255',
            'dehyar_bio'       => 'nullable|string|max:2000',
            'dehyar_phone'     => 'nullable|string|max:50',
            'dehyar_email'     => 'nullable|email|max:100',
            'dehyar_instagram' => 'nullable|string|max:255',
            'dehyar_hours'     => 'nullable|string|max:100',
        ], [
            'dehyar_name.required' => 'نام دهیار الزامی است.',
        ]);

        $village->update($data);
        return back()->with('success', '✅ اطلاعات دهیار به‌روزرسانی شد.');
    }

}
