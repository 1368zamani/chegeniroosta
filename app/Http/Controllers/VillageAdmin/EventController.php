<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageEvent;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $events = VillageEvent::where('village_id', $village->id)
            ->orderBy('event_date', 'desc')
            ->paginate(20);
        return view('admin.village.events.index', compact('village', 'events'));
    }

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        return view('admin.village.events.form', compact('village'));
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'event_date'   => 'required|date',
            'event_time'   => 'nullable|string',
            'event_type'   => 'nullable|string|max:50',
            'is_important' => 'nullable|boolean',
        ]);
        $data['village_id'] = $village->id;
        $data['is_important'] = $request->boolean('is_important');
        VillageEvent::create($data);
        return redirect()->route('admin.village.events.index', $village->id)
            ->with('success', 'رویداد ثبت شد.');
    }

    public function edit($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $event = VillageEvent::where('village_id', $village->id)->findOrFail($id);
        return view('admin.village.events.form', compact('village', 'event'));
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $event = VillageEvent::where('village_id', $village->id)->findOrFail($id);
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'event_date'   => 'required|date',
            'event_time'   => 'nullable|string',
            'event_type'   => 'nullable|string|max:50',
            'is_important' => 'nullable|boolean',
        ]);
        $data['is_important'] = $request->boolean('is_important');
        $event->update($data);
        return redirect()->route('admin.village.events.index', $village->id)
            ->with('success', 'رویداد به‌روزرسانی شد.');
    }

    public function destroy($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        VillageEvent::where('village_id', $village->id)->findOrFail($id)->delete();
        return back()->with('success', 'رویداد حذف شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }
}
