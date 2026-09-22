<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageFacility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $facilities = VillageFacility::where('village_id', $village->id)
            ->orderBy('sort_order')->paginate(50);
        return view('admin.village.facilities.index', compact('village', 'facilities'));
    }

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        return view('admin.village.facilities.form', compact('village'));
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'icon'         => 'nullable|string|max:20',
            'category'     => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'is_available' => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
        ]);
        $data['village_id'] = $village->id;
        $data['is_available'] = $request->boolean('is_available', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        VillageFacility::create($data);
        return redirect()->route('admin.village.facilities.index', $village->id)
            ->with('success', 'امکانات اضافه شد.');
    }

    public function edit($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $facility = VillageFacility::where('village_id', $village->id)->findOrFail($id);
        return view('admin.village.facilities.form', compact('village', 'facility'));
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $facility = VillageFacility::where('village_id', $village->id)->findOrFail($id);
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'icon'         => 'nullable|string|max:20',
            'category'     => 'nullable|string|max:100',
            'description'  => 'nullable|string',
            'is_available' => 'nullable|boolean',
            'sort_order'   => 'nullable|integer',
        ]);
        $data['is_available'] = $request->boolean('is_available');
        $facility->update($data);
        return redirect()->route('admin.village.facilities.index', $village->id)
            ->with('success', 'امکانات به‌روزرسانی شد.');
    }

    public function destroy($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        VillageFacility::where('village_id', $village->id)->findOrFail($id)->delete();
        return back()->with('success', 'امکانات حذف شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }
}
