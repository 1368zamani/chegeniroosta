<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $projects = VillageProject::where('village_id', $village->id)
            ->orderBy('sort_order')
            ->paginate(20);
        return view('admin.village.projects.index', compact('village', 'projects'));
    }

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        return view('admin.village.projects.form', compact('village'));
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'required|in:planned,in_progress,done,paused',
            'progress'     => 'nullable|integer|min:0|max:100',
            'contractor'   => 'nullable|string|max:255',
            'budget'       => 'nullable|string|max:255',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date',
            'cover_image'  => 'nullable|image|max:5120',
            'before_image' => 'nullable|image|max:5120',
            'after_image'  => 'nullable|image|max:5120',
            'sort_order'   => 'nullable|integer',
        ]);

        $data['village_id'] = $village->id;
        $data['progress'] = $data['progress'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        foreach (['cover_image', 'before_image', 'after_image'] as $img) {
            if ($request->hasFile($img)) {
                $data[$img] = $request->file($img)->store('village-projects/' . $village->id, 'public');
            }
        }

        VillageProject::create($data);

        return redirect()->route('admin.village.projects.index', $village->id)
            ->with('success', 'پروژه ثبت شد.');
    }

    public function edit($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $project = VillageProject::where('village_id', $village->id)->findOrFail($id);
        return view('admin.village.projects.form', compact('village', 'project'));
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $project = VillageProject::where('village_id', $village->id)->findOrFail($id);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'required|in:planned,in_progress,done,paused',
            'progress'     => 'nullable|integer|min:0|max:100',
            'contractor'   => 'nullable|string|max:255',
            'budget'       => 'nullable|string|max:255',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date',
            'cover_image'  => 'nullable|image|max:5120',
            'before_image' => 'nullable|image|max:5120',
            'after_image'  => 'nullable|image|max:5120',
            'sort_order'   => 'nullable|integer',
        ]);

        foreach (['cover_image', 'before_image', 'after_image'] as $img) {
            if ($request->hasFile($img)) {
                if ($project->$img && Storage::disk('public')->exists($project->$img)) {
                    Storage::disk('public')->delete($project->$img);
                }
                $data[$img] = $request->file($img)->store('village-projects/' . $village->id, 'public');
            }
        }

        $project->update($data);

        return redirect()->route('admin.village.projects.index', $village->id)
            ->with('success', 'پروژه به‌روزرسانی شد.');
    }

    public function destroy($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $project = VillageProject::where('village_id', $village->id)->findOrFail($id);
        $project->delete();
        return back()->with('success', 'پروژه حذف شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }
}
