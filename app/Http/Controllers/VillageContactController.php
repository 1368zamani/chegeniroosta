<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\VillageContact;
use Illuminate\Http\Request;

class VillageContactController extends Controller
{
    // یک رکورد به‌ازای هر روستا — اگر وجود نداشت، فرم خالی نشان می‌دهیم
    public function edit(Village $village)
    {
        $contact = $village->contact ?? new VillageContact();

        return view('admin.contacts.edit', compact('village', 'contact'));
    }

    public function update(Request $request, Village $village)
    {
        $validated = $request->validate([
            'address'       => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'email'         => ['nullable', 'email', 'max:255'],
            'working_hours' => ['nullable', 'string', 'max:255'],
            'map_embed_url' => ['nullable', 'url', 'max:2048'],
        ]);

        VillageContact::updateOrCreate(
            ['village_id' => $village->id],
            $validated
        );

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'اطلاعات تماس ذخیره شد.');
    }
}
