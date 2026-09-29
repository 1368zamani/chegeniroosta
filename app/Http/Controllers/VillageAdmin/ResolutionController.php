<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageCouncilResolution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResolutionController extends Controller
{
    public function index($villageId, Request $request)
    {
        $village = $this->authorizeVillage($villageId);

        $query = VillageCouncilResolution::where('village_id', $village->id);

        if ($request->filled('status') && in_array($request->status, ['approved','in_progress','completed','suspended','rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('session_number', 'like', "%{$q}%");
            });
        }

        $resolutions = $query->orderBy('sort_order')
            ->orderBy('session_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)->withQueryString();

        $stats = [
            'total'       => VillageCouncilResolution::where('village_id', $village->id)->count(),
            'approved'    => VillageCouncilResolution::where('village_id', $village->id)->where('status', 'approved')->count(),
            'in_progress' => VillageCouncilResolution::where('village_id', $village->id)->where('status', 'in_progress')->count(),
            'completed'   => VillageCouncilResolution::where('village_id', $village->id)->where('status', 'completed')->count(),
        ];

        return view('admin.village.resolutions.index', compact('village', 'resolutions', 'stats'));
    }

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        return view('admin.village.resolutions.form', compact('village'));
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $data = $request->validate([
            'session_number'   => 'nullable|string|max:50',
            'session_date'     => 'nullable|date',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string|max:5000',
            'status'           => 'required|in:approved,in_progress,completed,suspended,rejected',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'budget'           => 'nullable|numeric|min:0',
            'deadline'         => 'nullable|date',
            'admin_note'       => 'nullable|string|max:3000',
            'attachment'       => 'nullable|file|max:25600',
            'is_public'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer',
        ]);

        $data['village_id'] = $village->id;
        $data['user_id'] = auth()->id();
        $data['is_public'] = $request->boolean('is_public', true);
        $data['progress_percent'] = $data['progress_percent'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('village-resolutions/' . $village->id, 'public');
        }

        VillageCouncilResolution::create($data);

        return redirect()->route('admin.village.resolutions.index', $village->id)
            ->with('success', 'مصوبه با موفقیت ثبت شد.');
    }

    public function edit($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $resolution = VillageCouncilResolution::where('village_id', $village->id)->findOrFail($id);
        return view('admin.village.resolutions.form', compact('village', 'resolution'));
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $resolution = VillageCouncilResolution::where('village_id', $village->id)->findOrFail($id);

        $data = $request->validate([
            'session_number'   => 'nullable|string|max:50',
            'session_date'     => 'nullable|date',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string|max:5000',
            'status'           => 'required|in:approved,in_progress,completed,suspended,rejected',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'budget'           => 'nullable|numeric|min:0',
            'deadline'         => 'nullable|date',
            'admin_note'       => 'nullable|string|max:3000',
            'attachment'       => 'nullable|file|max:25600',
            'is_public'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer',
        ]);

        $data['is_public'] = $request->boolean('is_public');
        $data['progress_percent'] = $data['progress_percent'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('attachment')) {
            if ($resolution->attachment && Storage::disk('public')->exists($resolution->attachment)) {
                Storage::disk('public')->delete($resolution->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('village-resolutions/' . $village->id, 'public');
        }

        $resolution->update($data);

        return redirect()->route('admin.village.resolutions.index', $village->id)
            ->with('success', 'مصوبه به‌روزرسانی شد.');
    }

    public function destroy($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $resolution = VillageCouncilResolution::where('village_id', $village->id)->findOrFail($id);

        if ($resolution->attachment && Storage::disk('public')->exists($resolution->attachment)) {
            Storage::disk('public')->delete($resolution->attachment);
        }

        $resolution->delete();

        return back()->with('success', 'مصوبه حذف شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }
}
