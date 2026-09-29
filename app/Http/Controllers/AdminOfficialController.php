<?php

namespace App\Http\Controllers;

use App\Models\CountyOfficial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminOfficialController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);

        $officials = CountyOfficial::orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.officials.index', compact('officials'));
    }

    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        return view('admin.officials.form');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);

        $data = $request->validate([
            'type'       => 'required|in:governor,deputy',
            'name'       => 'required|string|max:255',
            'position'   => 'required|string|max:100',
            'photo'      => 'nullable|image|max:5120',
            'bio'        => 'nullable|string|max:3000',
            'education'  => 'nullable|string|max:2000',
            'experience' => 'nullable|string|max:3000',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:100',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('officials', 'public');
        }

        CountyOfficial::create($data);

        return redirect()->route('admin.officials.index')
            ->with('success', 'مسئول با موفقیت اضافه شد.');
    }

    public function edit($id)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $official = CountyOfficial::findOrFail($id);
        return view('admin.officials.form', compact('official'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $official = CountyOfficial::findOrFail($id);

        $data = $request->validate([
            'type'       => 'required|in:governor,deputy',
            'name'       => 'required|string|max:255',
            'position'   => 'required|string|max:100',
            'photo'      => 'nullable|image|max:5120',
            'bio'        => 'nullable|string|max:3000',
            'education'  => 'nullable|string|max:2000',
            'experience' => 'nullable|string|max:3000',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:100',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            if ($official->photo && Storage::disk('public')->exists($official->photo)) {
                Storage::disk('public')->delete($official->photo);
            }
            $data['photo'] = $request->file('photo')->store('officials', 'public');
        }

        $official->update($data);

        return redirect()->route('admin.officials.index')
            ->with('success', 'اطلاعات به‌روزرسانی شد.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->isSuperAdmin()) abort(403);
        $official = CountyOfficial::findOrFail($id);

        if ($official->photo && Storage::disk('public')->exists($official->photo)) {
            Storage::disk('public')->delete($official->photo);
        }

        $official->delete();
        return back()->with('success', 'حذف شد.');
    }
}
