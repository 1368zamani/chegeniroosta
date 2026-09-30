<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    private function authorizeAdmin()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isSectionAdmin()) {
            abort(403, 'دسترسی ندارید.');
        }
    }

    public function index()
    {
        $this->authorizeAdmin();
        $categories = FeeCategory::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.fees.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.fees.categories.form');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'slug'        => 'required|string|max:50|unique:fee_categories,slug',
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        FeeCategory::create($data);

        return redirect()->route('admin.fees.categories.index')
            ->with('success', 'دسته‌بندی اضافه شد.');
    }

    public function edit($id)
    {
        $this->authorizeAdmin();
        $category = FeeCategory::findOrFail($id);
        return view('admin.fees.categories.form', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $category = FeeCategory::findOrFail($id);

        $data = $request->validate([
            'slug'        => 'required|string|max:50|unique:fee_categories,slug,' . $id,
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $category->update($data);

        return redirect()->route('admin.fees.categories.index')
            ->with('success', 'دسته‌بندی به‌روزرسانی شد.');
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();
        $category = FeeCategory::findOrFail($id);

        if ($category->feeTypes()->count() > 0) {
            return back()->withErrors(['error' => 'ابتدا عناوین خدمت این دسته را حذف کنید.']);
        }

        $category->delete();
        return back()->with('success', 'دسته‌بندی حذف شد.');
    }
}
