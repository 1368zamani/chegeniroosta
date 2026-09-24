<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index($villageId, Request $request)
    {
        $village = $this->authorizeVillage($villageId);

        $query = VillageRequest::where('village_id', $village->id);

        if ($request->filled('status') && in_array($request->status, ['submitted','reviewing','assigned','processing','resolved','rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('tracking_code', 'like', "%{$q}%")
                    ->orWhere('full_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total'       => VillageRequest::where('village_id', $village->id)->count(),
            'pending'     => VillageRequest::where('village_id', $village->id)->whereIn('status', ['submitted','reviewing','assigned','processing'])->count(),
            'resolved'    => VillageRequest::where('village_id', $village->id)->where('status', 'resolved')->count(),
            'rejected'    => VillageRequest::where('village_id', $village->id)->where('status', 'rejected')->count(),
        ];

        return view('admin.village.requests.index', compact('village', 'requests', 'stats'));
    }

    public function show($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $request = VillageRequest::where('village_id', $village->id)->findOrFail($id);

        return view('admin.village.requests.show', compact('village', 'request'));
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $villageRequest = VillageRequest::where('village_id', $village->id)->findOrFail($id);

        $data = $request->validate([
            'status'         => 'required|in:submitted,reviewing,assigned,processing,resolved,rejected',
            'admin_response' => 'nullable|string|max:3000',
        ]);

        if ($data['status'] === 'resolved') {
            $data['resolved_at'] = now();
        }

        $villageRequest->update($data);

        return redirect()
            ->route('admin.village.requests.show', [$village->id, $villageRequest->id])
            ->with('success', 'درخواست به‌روزرسانی شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }
}
