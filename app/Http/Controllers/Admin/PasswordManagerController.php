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
        if (!$u || (!$u->isSuperAdmin() && !$u->isSectionAdmin())) {
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
}
