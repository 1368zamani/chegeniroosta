<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCategory;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    private function authorizeAdmin()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isSectionAdmin()) {
            abort(403, 'دسترسی ندارید.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = FeeType::with('category')->orderBy('category_id')->orderBy('sort_order');
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $types = $query->get();
        $categories = FeeCategory::orderBy('sort_order')->get();

        return view('admin.fees.types.index', compact('types', 'categories'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $categories = FeeCategory::orderBy('sort_order')->get();
        return view('admin.fees.types.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'category_id' => 'required|exists:fee_categories,id',
            'slug'        => 'nullable|string|max:100',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        FeeType::create($data);

        return redirect()->route('admin.fees.types.index')
            ->with('success', 'عنوان خدمت اضافه شد.');
    }

    public function edit($id)
    {
        $this->authorizeAdmin();
        $type = FeeType::findOrFail($id);
        $categories = FeeCategory::orderBy('sort_order')->get();
        return view('admin.fees.types.form', compact('type', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $type = FeeType::findOrFail($id);

        $data = $request->validate([
            'category_id' => 'required|exists:fee_categories,id',
            'slug'        => 'nullable|string|max:100',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $type->update($data);

        return redirect()->route('admin.fees.types.index')
            ->with('success', 'عنوان خدمت به‌روزرسانی شد.');
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();
        $type = FeeType::findOrFail($id);

        if ($type->tariffs()->count() > 0) {
            return back()->withErrors(['error' => 'این عنوان تعرفه دارد، ابتدا تعرفه‌ها را حذف کنید.']);
        }

        $type->delete();
        return back()->with('success', 'عنوان خدمت حذف شد.');
    }
}
