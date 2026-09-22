<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageCouncilMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CouncilController extends Controller
{
    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $members = VillageCouncilMember::where('village_id', $village->id)
            ->orderBy('sort_order')->get();
        return view('admin.village.council.index', compact('village', 'members'));
    }

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        return view('admin.village.council.form', compact('village'));
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'position'   => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'photo'      => 'nullable|image|max:2048',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['village_id'] = $village->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('village-council/' . $village->id, 'public');
        }
        VillageCouncilMember::create($data);
        return redirect()->route('admin.village.council.index', $village->id)
            ->with('success', 'عضو شورا اضافه شد.');
    }

    public function edit($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $member = VillageCouncilMember::where('village_id', $village->id)->findOrFail($id);
        return view('admin.village.council.form', compact('village', 'member'));
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $member = VillageCouncilMember::where('village_id', $village->id)->findOrFail($id);
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'position'   => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'photo'      => 'nullable|image|max:2048',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('photo')) {
            if ($member->photo && Storage::disk('public')->exists($member->photo)) {
                Storage::disk('public')->delete($member->photo);
            }
            $data['photo'] = $request->file('photo')->store('village-council/' . $village->id, 'public');
        }
        $member->update($data);
        return redirect()->route('admin.village.council.index', $village->id)
            ->with('success', 'اطلاعات عضو شورا به‌روزرسانی شد.');
    }

    public function destroy($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        VillageCouncilMember::where('village_id', $village->id)->findOrFail($id)->delete();
        return back()->with('success', 'عضو شورا حذف شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }
}
