<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TariffOfficerController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        $u = auth()->user();
        if (!$u || !method_exists($u, 'isSuperAdmin') || !$u->isSuperAdmin()) {
            abort(403, 'فقط مدیر ارشد می‌تواند کارشناسان را مدیریت کند.');
        }
    }

    public function index()
    {
        $this->authorizeSuperAdmin();

        $officers = User::whereIn('role', ['section_admin', 'fee_officer'])
            ->orWhere('role', 'tariff_officer')
            ->orderByDesc('id')
            ->paginate(30);

        $sections = Section::orderBy('name')->get();

        return view('admin.tariff-officers.index', compact('officers', 'sections'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'username'   => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z0-9_.-]+$/', 'unique:users,username'],
            'password'   => ['required', 'string', 'min:6', 'max:100'],
            'section_id' => ['required', 'exists:sections,id'],
            'role'       => ['required', Rule::in(['section_admin', 'fee_officer', 'tariff_officer'])],
            'email'      => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'phone'      => ['nullable', 'string', 'max:20'],
        ], [
            'username.regex'    => 'نام کاربری فقط شامل حروف انگلیسی، اعداد، نقطه، خط تیره و زیرخط.',
            'username.unique'   => 'این نام کاربری قبلاً ثبت شده است.',
            'username.required' => 'نام کاربری الزامی است.',
            'password.min'      => 'رمز عبور حداقل ۶ کاراکتر.',
            'section_id.required' => 'انتخاب بخشداری الزامی است.',
        ]);

        User::create([
            'name'       => $data['name'],
            'username'   => strtolower($data['username']),
            'email'      => $data['email'] ?: null,
            'password'   => Hash::make($data['password']),
            'role'       => $data['role'],
            'section_id' => $data['section_id'],
            'phone'      => $data['phone'] ?? null,
        ]);

        return back()->with('success', "کارشناس «{$data['name']}» با نام کاربری «{$data['username']}» ساخته شد.");
    }

    public function update(Request $request, User $officer)
    {
        $this->authorizeSuperAdmin();
        abort_unless(in_array($officer->role, ['section_admin', 'fee_officer', 'tariff_officer'], true), 404);

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'username'   => ['required', 'string', 'max:60', 'regex:/^[a-zA-Z0-9_.-]+$/',
                                Rule::unique('users', 'username')->ignore($officer->id)],
            'password'   => ['nullable', 'string', 'min:6', 'max:100'],
            'section_id' => ['required', 'exists:sections,id'],
            'role'       => ['required', Rule::in(['section_admin', 'fee_officer', 'tariff_officer'])],
            'email'      => ['nullable', 'email', 'max:150',
                                Rule::unique('users', 'email')->ignore($officer->id)],
            'phone'      => ['nullable', 'string', 'max:20'],
            'is_active'  => ['boolean'],
        ]);

        $officer->name       = $data['name'];
        $officer->username   = strtolower($data['username']);
        $officer->email      = $data['email'] ?: null;
        $officer->section_id = $data['section_id'];
        $officer->role       = $data['role'];
        $officer->phone      = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $officer->password = Hash::make($data['password']);
        }

        if (\Schema::hasColumn('users', 'is_active')) {
            $officer->is_active = (bool) ($data['is_active'] ?? true);
        }

        $officer->save();

        return back()->with('success', "اطلاعات «{$officer->name}» به‌روزرسانی شد.");
    }

    public function destroy(User $officer)
    {
        $this->authorizeSuperAdmin();
        abort_unless(in_array($officer->role, ['section_admin', 'fee_officer', 'tariff_officer'], true), 404);

        $name = $officer->name;
        $officer->delete();

        return back()->with('success', "کارشناس «{$name}» حذف شد.");
    }

    public function resetPassword(Request $request, User $officer)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'new_password' => ['required', 'string', 'min:6', 'max:100'],
        ]);

        $officer->password = Hash::make($data['new_password']);
        $officer->save();

        return back()->with('success', "رمز عبور «{$officer->name}» تغییر یافت.");
    }
}
