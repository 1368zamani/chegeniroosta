<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffExemption;
use App\Models\Village;
use Illuminate\Http\Request;

class ExpertTariffContentController extends Controller
{
    // ═══ ITEMS — آیتم‌های تعرفه ═══
    public function items(Request $request)
    {
        $user = $request->user();
        $villageIds = $this->allowedVillageIds($user);

        if (empty($villageIds)) {
            return view('expert.content.items', [
                'items' => collect(), 'villages' => collect(),
                'villageId' => null, 'set' => null,
                'stats' => ['total' => 0, 'active' => 0, 'levy' => 0, 'service' => 0],
            ]);
        }

        $villageId = (int) ($request->query('village_id') ?: $villageIds[0]);
        if (!in_array($villageId, $villageIds)) $villageId = $villageIds[0];

        $set = TariffSet::with('village')->where('village_id', $villageId)
            ->where('status', 'published')
            ->orderByDesc('jalali_year')
            ->first();

        $items = collect();
        if ($set) {
            $items = TariffItem::with('title')
                ->where('tariff_set_id', $set->id)
                ->when($request->query('type'), fn($q) =>
                    $q->whereHas('title', fn($t) => $t->where('type', $request->query('type')))
                )
                ->when($request->query('q'), fn($q) =>
                    $q->where(function ($qq) use ($request) {
                        $qq->where('notes', 'like', '%' . $request->query('q') . '%')
                           ->orWhereHas('title', fn($t) => $t->where('name_fa', 'like', '%' . $request->query('q') . '%'));
                    })
                )
                ->orderBy('id')
                ->get();
        }

        $stats = [
            'total'   => $items->count(),
            'active'  => $items->where('is_active', true)->count(),
            'levy'    => $items->filter(fn($i) => optional($i->title)->type === 'levy')->count(),
            'service' => $items->filter(fn($i) => optional($i->title)->type === 'service_fee')->count(),
        ];

        $villages = Village::whereIn('id', $villageIds)->orderBy('name')->get();

        return view('expert.content.items', compact('items', 'villages', 'villageId', 'set', 'stats'));
    }

    public function editItem(Request $request, int $id)
    {
        $item = TariffItem::with('title')->findOrFail($id);
        $set = TariffSet::with('village')->findOrFail($item->tariff_set_id);
        $this->authorizeVillage($request->user(), $set->village_id);
        return view('expert.content.edit-item', compact('item', 'set'));
    }

    public function updateItem(Request $request, int $id)
    {
        $item = TariffItem::findOrFail($id);
        $set = TariffSet::findOrFail($item->tariff_set_id);
        $this->authorizeVillage($request->user(), $set->village_id);

        $data = $request->validate([
            'previous_amount'  => ['nullable', 'integer', 'min:0'],
            'increase_percent' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'final_amount'     => ['required', 'integer', 'min:0'],
            'is_active'        => ['boolean'],
            'notes'            => ['nullable', 'string', 'max:2000'],
        ]);

        $item->update([
            'previous_amount'  => $data['previous_amount'] ?? 0,
            'increase_percent' => $data['increase_percent'] ?? 0,
            'final_amount'     => $data['final_amount'],
            'is_active'        => $request->boolean('is_active'),
            'notes'            => $data['notes'] ?? null,
        ]);

        return redirect()->route('expert.items.index', ['village_id' => $set->village_id])
            ->with('success', 'آیتم با موفقیت به‌روز شد.');
    }

    // ═══ PARAMETERS — مفروضات (ستون: key) ═══
    public function parameters(Request $request)
    {
        $user = $request->user();
        $villageIds = $this->allowedVillageIds($user);

        if (empty($villageIds)) {
            return view('expert.content.parameters', [
                'parameters' => collect(), 'villages' => collect(),
                'villageId' => null, 'set' => null,
            ]);
        }

        $villageId = (int) ($request->query('village_id') ?: $villageIds[0]);
        if (!in_array($villageId, $villageIds)) $villageId = $villageIds[0];

        $set = TariffSet::with('village')->where('village_id', $villageId)
            ->where('status', 'published')
            ->orderByDesc('jalali_year')
            ->first();

        $parameters = collect();
        if ($set) {
            $parameters = TariffParameter::where('tariff_set_id', $set->id)
                ->orderBy('key')
                ->get();
        }

        $villages = Village::whereIn('id', $villageIds)->orderBy('name')->get();

        return view('expert.content.parameters', compact('parameters', 'villages', 'villageId', 'set'));
    }

    public function editParameter(Request $request, int $id)
    {
        $parameter = TariffParameter::findOrFail($id);
        $set = TariffSet::with('village')->findOrFail($parameter->tariff_set_id);
        $this->authorizeVillage($request->user(), $set->village_id);
        return view('expert.content.edit-parameter', compact('parameter', 'set'));
    }

    public function updateParameter(Request $request, int $id)
    {
        $parameter = TariffParameter::findOrFail($id);
        $set = TariffSet::findOrFail($parameter->tariff_set_id);
        $this->authorizeVillage($request->user(), $set->village_id);

        $data = $request->validate([
            'value'       => ['required', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $parameter->update([
            'value'       => $data['value'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('expert.parameters.index', ['village_id' => $set->village_id])
            ->with('success', 'پارامتر به‌روز شد.');
    }

    // ═══ EXEMPTIONS — معافیت‌ها (ستون: kind) ═══
    public function exemptions(Request $request)
    {
        $user = $request->user();
        $villageIds = $this->allowedVillageIds($user);

        if (empty($villageIds)) {
            return view('expert.content.exemptions', [
                'exemptions' => collect(), 'villages' => collect(),
                'villageId' => null, 'set' => null,
            ]);
        }

        $villageId = (int) ($request->query('village_id') ?: $villageIds[0]);
        if (!in_array($villageId, $villageIds)) $villageId = $villageIds[0];

        $set = TariffSet::with('village')->where('village_id', $villageId)
            ->where('status', 'published')
            ->orderByDesc('jalali_year')
            ->first();

        $exemptions = collect();
        if ($set) {
            $exemptions = TariffExemption::with('title')
                ->where('tariff_set_id', $set->id)
                ->orderBy('id')
                ->get();
        }

        $villages = Village::whereIn('id', $villageIds)->orderBy('name')->get();

        return view('expert.content.exemptions', compact('exemptions', 'villages', 'villageId', 'set'));
    }

    public function editExemption(Request $request, int $id)
    {
        $exemption = TariffExemption::with('title')->findOrFail($id);
        $set = TariffSet::with('village')->findOrFail($exemption->tariff_set_id);
        $this->authorizeVillage($request->user(), $set->village_id);
        return view('expert.content.edit-exemption', compact('exemption', 'set'));
    }

    public function updateExemption(Request $request, int $id)
    {
        $exemption = TariffExemption::findOrFail($id);
        $set = TariffSet::findOrFail($exemption->tariff_set_id);
        $this->authorizeVillage($request->user(), $set->village_id);

        $data = $request->validate([
            'kind'             => ['required', 'string', 'max:100'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'description'      => ['required', 'string', 'max:500'],
            'statutory_note'   => ['nullable', 'string', 'max:2000'],
            'is_active'        => ['boolean'],
        ]);

        $exemption->update([
            'kind'             => $data['kind'],
            'discount_percent' => $data['discount_percent'],
            'description'      => $data['description'],
            'statutory_note'   => $data['statutory_note'] ?? null,
            'is_active'        => $request->boolean('is_active'),
        ]);

        return redirect()->route('expert.exemptions.index', ['village_id' => $set->village_id])
            ->with('success', 'معافیت به‌روز شد.');
    }

    // ═══ HELPERS ═══
    private function allowedVillageIds($user): array
    {
        if ($user->role === 'super_admin') return Village::pluck('id')->toArray();
        if ($user->role === 'section_admin' && $user->section_id)
            return Village::where('section_id', $user->section_id)->pluck('id')->toArray();
        if ($user->role === 'village_admin' && $user->village_id)
            return [(int) $user->village_id];
        if ($user->role === 'fee_officer' && $user->section_id)
            return Village::where('section_id', $user->section_id)->pluck('id')->toArray();
        return [];
    }

    private function authorizeVillage($user, int $villageId): void
    {
        if (!in_array((int) $villageId, $this->allowedVillageIds($user), true)) {
            abort(403, 'شما به این روستا دسترسی ندارید.');
        }
    }
}
