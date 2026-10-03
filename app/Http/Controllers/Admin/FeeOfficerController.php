<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * مدیریت کارشناسان مالی و فنی/عمرانی بخشداری.
 * - سوپرادمین: برای همه بخش‌ها
 * - بخشدار: فقط برای بخش خودش
 */
class FeeOfficerController extends Controller
{
    private function authorizeManager(): void
    {
        $u = auth()->user();
        if (!$u || (!$u->isSuperAdmin() && !$u->isSectionAdmin())) {
            abort(403, 'دسترسی ندارید.');
        }
    }

    /** کاربران کارشناس قابل مدیریت */
    private function scopedOfficers()
    {
        $u = auth()->user();

        $query = User::where('role', 'fee_officer');

        if ($u->isSectionAdmin()) {
            $query->where('section_id', $u->section_id);
        }

        return $query;
    }

    /** لیست کارشناسان */
    public function index(Request $request)
    {
        $this->authorizeManager();

        $query = $this->scopedOfficers()->with('section');

        // فیلتر بخش (فقط برای سوپرادمین)
        if (auth()->user()->isSuperAdmin() && $request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // فیلتر نوع
        if ($request->filled('type')) {
            $query->where('officer_type', $request->type);
        }

        // جستجو
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%");
            });
        }

        $officers = $query->orderBy('section_id')->orderBy('officer_type')->paginate(30)->withQueryString();

        // برای سوپرادمین: لیست همه بخش‌ها
        // برای بخشدار: فقط بخش خودش
        $sections = auth()->user()->isSuperAdmin()
            ? Section::orderBy('name')->get()
            : Section::where('id', auth()->user()->section_id)->get();

        return view('admin.fees.officers', compact('officers', 'sections'));
    }

    /** ایجاد کارشناس جدید */
    public function store(Request $request)
    {
        $this->authorizeManager();
        $u = auth()->user();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|string|min:8',
            'officer_type' => 'required|in:financial,technical',
            'section_id'   => $u->isSuperAdmin() ? 'required|exists:sections,id' : 'nullable',
            'phone'        => 'nullable|string|max:20',
            'position'     => 'nullable|string|max:100',
        ], [
            'email.unique'         => 'این ایمیل قبلاً ثبت شده است.',
            'password.min'         => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'officer_type.required'=> 'نوع کارشناس الزامی است.',
            'section_id.required'  => 'انتخاب بخش الزامی است.',
        ]);

        $sectionId = $u->isSectionAdmin() ? $u->section_id : $data['section_id'];

        User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'role'         => 'fee_officer',
            'officer_type' => $data['officer_type'],
            'section_id'   => $sectionId,
            'phone'        => $data['phone'] ?? null,
            'position'     => $data['position'] ?? null,
            'is_active'    => true,
        ]);

        $typeLabel = $data['officer_type'] === 'financial' ? 'مالی' : 'فنی/عمرانی';
        return back()->with('success', "✅ کارشناس $typeLabel جدید ساخته شد.");
    }

    /** تغییر رمز */
    public function updatePassword(Request $request, int $id)
    {
        $this->authorizeManager();
        $officer = $this->scopedOfficers()->findOrFail($id);

        $data = $request->validate([
            'password' => 'required|string|min:8',
        ], ['password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.']);

        $officer->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', "✅ رمز «{$officer->name}» به‌روزرسانی شد.");
    }

    /** ویرایش اطلاعات */
    public function update(Request $request, int $id)
    {
        $this->authorizeManager();
        $officer = $this->scopedOfficers()->findOrFail($id);

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'officer_type' => 'required|in:financial,technical',
            'phone'        => 'nullable|string|max:20',
            'position'     => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
        ]);

        $officer->update([
            'name'         => $data['name'],
            'officer_type' => $data['officer_type'],
            'phone'        => $data['phone'] ?? null,
            'position'     => $data['position'] ?? null,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return back()->with('success', '✅ اطلاعات کارشناس به‌روزرسانی شد.');
    }

    /** حذف */
    public function destroy(int $id)
    {
        $this->authorizeManager();
        $officer = $this->scopedOfficers()->findOrFail($id);
        $name = $officer->name;
        $officer->delete();

        return back()->with('success', "✅ کارشناس «$name» حذف شد.");
    }
}
