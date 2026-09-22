<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    private function ensureManagerAccess(): void
    {
        $u = auth()->user();
        if (!$u->isSuperAdmin() && !$u->isSectionAdmin()) {
            abort(403, 'دسترسی ندارید.');
        }
    }

    private function isSuper(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    private function sectionId(): ?int
    {
        return $this->isSuper() ? null : (int) auth()->user()->section_id;
    }

    public function index()
    {
        $this->ensureManagerAccess();

        $q = User::with(['section', 'village'])->orderBy('id', 'desc');
        if (!$this->isSuper()) {
            $q->where('section_id', $this->sectionId());
        }

        $users = $q->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->ensureManagerAccess();

        if ($this->isSuper()) {
            $sections = Section::orderBy('name')->get();
            $villages = Village::orderBy('name')->get();
        } else {
            $sid = $this->sectionId();
            $sections = Section::where('id', $sid)->get();
            $villages = Village::where('section_id', $sid)->orderBy('name')->get();
        }

        return view('admin.users.create', compact('sections', 'villages'));
    }

    public function store(Request $request)
    {
        $this->ensureManagerAccess();

        $allowedRoles = $this->isSuper()
            ? 'in:super_admin,section_admin,village_admin'
            : 'in:village_admin';

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|' . $allowedRoles,
            'section_id' => 'nullable|exists:sections,id',
            'village_id' => 'nullable|exists:villages,id',
        ]);

        if ($data['role'] === 'section_admin' && empty($data['section_id'])) {
            return back()->withErrors(['section_id' => 'برای ادمین بخش، انتخاب بخش الزامی است.'])->withInput();
        }
        if ($data['role'] === 'village_admin' && empty($data['village_id'])) {
            return back()->withErrors(['village_id' => 'برای ادمین روستا، انتخاب روستا الزامی است.'])->withInput();
        }

        if (!$this->isSuper()) {
            $data['section_id'] = $this->sectionId();
            $v = Village::find($data['village_id']);
            if (!$v || (int)$v->section_id !== $this->sectionId()) {
                abort(403, 'این روستا در بخش شما نیست.');
            }
            $data['role'] = 'village_admin';
        }

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'کاربر با موفقیت ساخته شد.');
    }

    public function edit($id)
    {
        $this->ensureManagerAccess();
        $user = $this->findUserInScopeOrFail($id);

        if ($this->isSuper()) {
            $sections = Section::orderBy('name')->get();
            $villages = Village::orderBy('name')->get();
        } else {
            $sid = $this->sectionId();
            $sections = Section::where('id', $sid)->get();
            $villages = Village::where('section_id', $sid)->orderBy('name')->get();
        }

        return view('admin.users.edit', compact('user', 'sections', 'villages'));
    }

    public function update(Request $request, $id)
    {
        $this->ensureManagerAccess();
        $user = $this->findUserInScopeOrFail($id);

        $allowedRoles = $this->isSuper()
            ? 'in:super_admin,section_admin,village_admin'
            : 'in:village_admin';

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'password'   => 'nullable|string|min:6',
            'role'       => 'required|' . $allowedRoles,
            'section_id' => 'nullable|exists:sections,id',
            'village_id' => 'nullable|exists:villages,id',
        ]);

        if ($data['role'] === 'section_admin' && empty($data['section_id'])) {
            return back()->withErrors(['section_id' => 'برای ادمین بخش، انتخاب بخش الزامی است.'])->withInput();
        }
        if ($data['role'] === 'village_admin' && empty($data['village_id'])) {
            return back()->withErrors(['village_id' => 'برای ادمین روستا، انتخاب روستا الزامی است.'])->withInput();
        }

        if (!$this->isSuper()) {
            $data['section_id'] = $this->sectionId();
            $v = Village::find($data['village_id']);
            if (!$v || (int)$v->section_id !== $this->sectionId()) {
                abort(403, 'این روستا در بخش شما نیست.');
            }
            $data['role'] = 'village_admin';
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
        $this->ensureManagerAccess();
        $user = $this->findUserInScopeOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'نمی‌توانید حساب خودتان را حذف کنید.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'کاربر حذف شد.');
    }

    private function findUserInScopeOrFail($id): User
    {
        $u = User::findOrFail($id);
        if ($this->isSuper()) return $u;

        if ((int)$u->section_id !== $this->sectionId() || $u->isSuperAdmin()) {
            abort(403, 'این کاربر در بخش شما نیست.');
        }
        return $u;
    }
}
