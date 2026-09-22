<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageAttraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttractionController extends Controller
{
    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $attractions = VillageAttraction::where('village_id', $village->id)
            ->orderBy('sort_order')->get();
        return view('admin.village.attractions.index', compact('village', 'attractions'));
    }

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        return view('admin.village.attractions.form', compact('village'));
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
            'address'     => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);
        $data['village_id'] = $village->id;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('village-attractions/' . $village->id, 'public');
        }
        VillageAttraction::create($data);
        return redirect()->route('admin.village.attractions.index', $village->id)
            ->with('success', 'جاذبه اضافه شد.');
    }

    public function edit($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $attraction = VillageAttraction::where('village_id', $village->id)->findOrFail($id);
        return view('admin.village.attractions.form', compact('village', 'attraction'));
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $attraction = VillageAttraction::where('village_id', $village->id)->findOrFail($id);
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
            'address'     => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);
        if ($request->hasFile('image')) {
            if ($attraction->image && Storage::disk('public')->exists($attraction->image)) {
                Storage::disk('public')->delete($attraction->image);
            }
            $data['image'] = $request->file('image')->store('village-attractions/' . $village->id, 'public');
        }
        $attraction->update($data);
        return redirect()->route('admin.village.attractions.index', $village->id)
            ->with('success', 'جاذبه به‌روزرسانی شد.');
    }

    public function destroy($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        VillageAttraction::where('village_id', $village->id)->findOrFail($id)->delete();
        return back()->with('success', 'جاذبه حذف شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }
}
