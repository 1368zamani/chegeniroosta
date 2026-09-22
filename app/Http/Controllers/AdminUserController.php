<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'فقط سوپر ادمین می‌تواند کاربران را مدیریت کند.');
        }

        $users = User::with(['section', 'village'])->orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $sections = Section::orderBy('name')->get();
        $villages = Village::orderBy('name')->get();
        return view('admin.users.create', compact('sections', 'villages'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:super_admin,section_admin,village_admin',
            'section_id' => 'nullable|exists:sections,id',
            'village_id' => 'nullable|exists:villages,id',
        ]);

        if ($data['role'] === 'section_admin' && empty($data['section_id'])) {
            return back()->withErrors(['section_id' => 'برای ادمین بخش، انتخاب بخش الزامی است.'])->withInput();
        }

        if ($data['role'] === 'village_admin' && empty($data['village_id'])) {
            return back()->withErrors(['village_id' => 'برای ادمین روستا، انتخاب روستا الزامی است.'])->withInput();
        }

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'کاربر با موفقیت ساخته شد.');
    }

    public function edit($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);
        $sections = Section::orderBy('name')->get();
        $villages = Village::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'sections', 'villages'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'password'   => 'nullable|string|min:6',
            'role'       => 'required|in:super_admin,section_admin,village_admin',
            'section_id' => 'nullable|exists:sections,id',
            'village_id' => 'nullable|exists:villages,id',
        ]);

        if ($data['role'] === 'section_admin' && empty($data['section_id'])) {
            return back()->withErrors(['section_id' => 'برای ادمین بخش، انتخاب بخش الزامی است.'])->withInput();
        }

        if ($data['role'] === 'village_admin' && empty($data['village_id'])) {
            return back()->withErrors(['village_id' => 'برای ادمین روستا، انتخاب روستا الزامی است.'])->withInput();
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'اطلاعات کاربر به‌روزرسانی شد.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'نمی‌توانید حساب خودتان را حذف کنید.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'کاربر حذف شد.');
    }
}
