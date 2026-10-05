<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordManagerController extends Controller
{
    /** فقط سوپرادمین و بخشدار مجاز */
    private function authorizeManager(): void
    {
        $u = auth()->user();
        if (!$u) {
            abort(403, 'دسترسی ندارید.');
        }

        // دهیار → داشبورد روستای خودش
        if ($u->isVillageAdmin() && $u->village_id) {
            redirect()->route('admin.village.dashboard', $u->village_id)->send();
            exit;
        }

        if (!$u->isSuperAdmin() && !$u->isSectionAdmin()) {
            abort(403, 'دسترسی ندارید.');
        }
    }

    /** کاربران قابل مدیریت بر اساس نقش */
    private function scopedUsers()
    {
        $u = auth()->user();

        if ($u->isSuperAdmin()) {
            return User::query();
        }

        if ($u->isSectionAdmin()) {
            // کاربران بخشداری خودش + روستاهای همان بخش
            $villageIds = Village::where('section_id', $u->section_id)->pluck('id');

            return User::where(function ($q) use ($u, $villageIds) {
                $q->where('section_id', $u->section_id)
                  ->orWhereIn('village_id', $villageIds);
            });
        }

        return User::whereRaw('1=0');
    }

    /** لیست کاربران */
    public function index(Request $request)
    {
        $this->authorizeManager();

        $query = $this->scopedUsers()->with(['section', 'village']);

        // فیلتر جستجو
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('email', 'like', "%$q%")
                  ->orWhere('name', 'like', "%$q%");
            });
        }

        // فیلتر نقش
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // فیلتر بخش
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $users = $query->orderBy('role')->orderBy('id')->paginate(30)->withQueryString();

        $sections = auth()->user()->isSuperAdmin()
            ? Section::orderBy('name')->get()
            : Section::where('id', auth()->user()->section_id)->get();

        return view('admin.passwords.index', compact('users', 'sections'));
    }

    /** ریست رمز یک کاربر */
    public function reset(Request $request, int $id)
    {
        $this->authorizeManager();

        $user = $this->scopedUsers()->findOrFail($id);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:64'],
        ], [
            'password.required' => 'رمز عبور جدید الزامی است.',
            'password.min'      => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', "✅ رمز «{$user->name}» ({$user->email}) با موفقیت تغییر کرد.");
    }

    /** تولید رمز تصادفی */
    public function generate()
    {
        $this->authorizeManager();

        $length = 12;
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789@#$%';
        $pass = '';
        for ($i = 0; $i < $length; $i++) {
            $pass .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return response()->json(['password' => $pass]);
    }

    /** ریست دسته‌جمعی */
    public function bulkReset(Request $request)
    {
        $this->authorizeManager();

        $data = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer',
            'password' => 'required|string|min:8',
        ]);

        $count = 0;
        foreach ($data['user_ids'] as $uid) {
            $u = $this->scopedUsers()->find($uid);
            if ($u) {
                $u->update(['password' => Hash::make($data['password'])]);
                $count++;
            }
        }

        return back()->with('success', "✅ رمز $count کاربر تغییر کرد.");
    }

    /** ایجاد کاربر جدید */
    public function store(Request $request)
    {
        $this->authorizeManager();
        $manager = auth()->user();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|string|min:8|max:64',
            'role'         => 'required|in:section_admin,fee_officer,village_admin,super_admin',
            'officer_type' => 'nullable|in:financial,technical',
            'section_id'   => 'nullable|exists:sections,id',
            'village_id'   => 'nullable|exists:villages,id',
            'phone'        => 'nullable|string|max:20',
        ], [
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        ]);

        // محدودیت سوپرادمین را فقط سوپرادمین می‌تواند بسازد
        if ($data['role'] === 'super_admin' && !$manager->isSuperAdmin()) {
            abort(403, 'فقط سوپرادمین می‌تواند سوپرادمین بسازد.');
        }

        // بخشدار فقط می‌تواند در بخش خودش کاربر بسازد
        $sectionId = $data['section_id'] ?? null;
        if ($manager->isSectionAdmin()) {
            $sectionId = $manager->section_id;
        }

        // اگر نقش دهیار است، village_id الزامی است
        if ($data['role'] === 'village_admin' && empty($data['village_id'])) {
            return back()->withErrors(['village_id' => 'برای دهیار، انتخاب روستا الزامی است.'])->withInput();
        }

        // اگر نقش کارشناس عوارض است، officer_type الزامی است
        if ($data['role'] === 'fee_officer' && empty($data['officer_type'])) {
            return back()->withErrors(['officer_type' => 'برای کارشناس، انتخاب نوع الزامی است.'])->withInput();
        }

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'role'         => $data['role'],
            'officer_type' => $data['officer_type'] ?? null,
            'section_id'   => $sectionId,
            'village_id'   => $data['village_id'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'is_active'    => true,
        ]);

        return back()->with('success', "✅ کاربر «{$user->name}» با نقش {$user->role} ساخته شد.");
    }

}
