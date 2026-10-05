<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffExemption;
use App\Models\TariffSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TariffExemptionController extends Controller
{
    public function index(TariffSet $tariffSet)
    {
        Gate::authorize('view', $tariffSet);
        $tariffSet->load(['village']);

        $exemptions = $tariffSet->exemptions()
            ->with('title', 'creator')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get();

        return view('admin.tariffs.exemptions.index', compact('tariffSet', 'exemptions'));
    }

    public function store(Request $request, TariffSet $tariffSet)
    {
        Gate::authorize('update', $tariffSet);

        $data = $request->validate([
            'kind'              => ['required', 'string', 'in:' . implode(',', array_keys(TariffExemption::kindOptions()))],
            'tariff_title_id'   => ['nullable', 'exists:tariff_titles,id'],
            'discount_percent'  => ['required', 'numeric', 'min:0', 'max:100'],
            'description'       => ['required', 'string', 'max:500'],
            'valid_from'        => ['nullable', 'string', 'max:10'],
            'valid_until'       => ['nullable', 'string', 'max:10'],
            'statutory_note'    => ['nullable', 'string', 'max:2000'],
            'is_active'         => ['boolean'],
        ]);

        $data['tariff_set_id'] = $tariffSet->id;
        $data['created_by']    = $request->user()->id;
        $data['is_active']     = (bool) ($data['is_active'] ?? true);

        TariffExemption::create($data);

        return back()->with('success', 'معافیت اضافه شد.');
    }

    public function update(Request $request, TariffSet $tariffSet, TariffExemption $exemption)
    {
        Gate::authorize('update', $tariffSet);
        abort_if($exemption->tariff_set_id !== $tariffSet->id, 404);

        $data = $request->validate([
            'kind'              => ['required', 'string', 'in:' . implode(',', array_keys(TariffExemption::kindOptions()))],
            'tariff_title_id'   => ['nullable', 'exists:tariff_titles,id'],
            'discount_percent'  => ['required', 'numeric', 'min:0', 'max:100'],
            'description'       => ['required', 'string', 'max:500'],
            'valid_from'        => ['nullable', 'string', 'max:10'],
            'valid_until'       => ['nullable', 'string', 'max:10'],
            'statutory_note'    => ['nullable', 'string', 'max:2000'],
            'is_active'         => ['boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $exemption->update($data);

        return back()->with('success', 'معافیت به‌روزرسانی شد.');
    }

    public function destroy(TariffSet $tariffSet, TariffExemption $exemption)
    {
        Gate::authorize('update', $tariffSet);
        abort_if($exemption->tariff_set_id !== $tariffSet->id, 404);

        $exemption->delete();

        return back()->with('success', 'معافیت حذف شد.');
    }
}
