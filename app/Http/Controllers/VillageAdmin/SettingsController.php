<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        return view('admin.village.settings', compact('village'));
    }

    public function update(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $fields = [
            'show_stats', 'show_news', 'show_projects', 'show_events',
            'show_facilities', 'show_council', 'show_attractions',
            'show_gallery', 'show_request_box', 'show_about', 'show_info'
        ];

        $data = [];
        foreach ($fields as $f) {
            $data[$f] = $request->boolean($f);
        }

        // اطلاعات پایه
        $baseData = $request->validate([
            'welcome_text'   => 'nullable|string',
            'history_text'   => 'nullable|string',
            'naming_story'   => 'nullable|string',
            'description'    => 'nullable|string',
            'dehyar_name'    => 'nullable|string|max:255',
            'dehyar_hours'   => 'nullable|string|max:255',
            'dehyar_instagram' => 'nullable|string|max:255',
            'council_phone'  => 'nullable|string|max:50',
            'phone'          => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:500',
            'bank_name'      => 'nullable|string|max:255',
            'bank_account'   => 'nullable|string|max:255',
            'payment_id'     => 'nullable|string|max:255',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ]);

        $village->update(array_merge($data, $baseData));

        return redirect()->route('admin.village.settings.index', $village->id)
            ->with('success', 'تنظیمات ذخیره شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }
}
