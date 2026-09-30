<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * مدیریت کارشناسان مالی و عمرانی بخشداری.
 * فقط بخشدار (section_admin) و سوپرادمین دسترسی دارند.
 */
class FeeOfficerController extends Controller
{
    private function authorizeSection(): void
    {
        $u = auth()->user();
        if (!$u || (!$u->isSuperAdmin() && !$u->isSectionAdmin())) {
            abort(403, 'دسترسی ندارید.');
        }
    }

    public function index()
    {
        $this->authorizeSection();
        $u = auth()->user();

        $query = User::where('role', 'fee_officer');

        if ($u->isSectionAdmin()) {
            $query->where('section_id', $u->section_id);
        }

        $officers = $query->orderBy('name')->get();

        return view('admin.fees.officers', compact('officers'));
    }

    public function store(Request $request)
    {
        $this->authorizeSection();
        $u = auth()->user();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        ]);

        User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'fee_officer',
            'section_id' => $u->isSectionAdmin() ? $u->section_id : null,
        ]);

        return back()->with('success', '✅ کارشناس جدید ساخته شد.');
    }

    public function updatePassword(Request $request, int $id)
    {
        $this->authorizeSection();
        $u = auth()->user();

        $query = User::where('role', 'fee_officer');
        if ($u->isSectionAdmin()) {
            $query->where('section_id', $u->section_id);
        }
        $officer = $query->findOrFail($id);

        $data = $request->validate([
            'password' => 'required|string|min:8',
        ], ['password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.']);

        $officer->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', '✅ رمز عبور کارشناس به‌روزرسانی شد.');
    }

    public function destroy(int $id)
    {
        $this->authorizeSection();
        $u = auth()->user();

        $query = User::where('role', 'fee_officer');
        if ($u->isSectionAdmin()) {
            $query->where('section_id', $u->section_id);
        }
        $officer = $query->findOrFail($id);
        $officer->delete();

        return back()->with('success', '✅ کارشناس حذف شد.');
    }
}
