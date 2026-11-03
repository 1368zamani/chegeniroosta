<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffParameter;
use App\Models\TariffSet;
use App\Models\TariffTitle;
use App\Models\Village;
use App\Services\Tariff\TariffSetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class TariffSetController extends Controller
{
    public function __construct(private TariffSetService $service) {}

    public function index(Request $request)
    {
        // 🔒 توجه: بررسی «حالت کارشناس» حذف شد.
        // کلید session('tariff_officer_mode') فقط در TariffOfficerAuthController
        // نوشته می‌شد و آن کنترلر به هیچ مسیری وصل نیست؛ در نتیجه این گیت
        // همیشه فعال بود و کل بخش تعرفه‌ها (ادمین و کارشناس) را قفل می‌کرد.
        // مجوزدهی هم‌اکنون توسط TariffSetPolicy انجام می‌شود.

        Gate::authorize('viewAny', TariffSet::class);
        $query = TariffSet::with('village')->orderByDesc('jalali_year')->orderByDesc('id');
        $user = $request->user();
        if ($user && method_exists($user, 'isVillageAdmin') && $user->isVillageAdmin() && $user->village_id) {
            $query->where('village_id', $user->village_id);
        } elseif ($user && method_exists($user, 'isSectionAdmin') && $user->isSectionAdmin() && ($user->section_id ?? null)) {
            $query->whereHas('village', fn($q) => $q->where('section_id', $user->section_id));
        }
        $sets = $query->paginate(20)->withQueryString();
        return view('admin.tariffs.index', compact('sets'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', TariffSet::class);
        $villages = $this->availableVillages($request->user());
        $previousSets = TariffSet::with('village')
            ->whereIn('village_id', $villages->pluck('id'))
            ->whereIn('status', ['published', 'finalized'])
            ->orderByDesc('jalali_year')->get();
        $suggestedYear = (int) (\Morilog\Jalali\Jalalian::now()->getYear() + 1);
        return view('admin.tariffs.create', compact('villages', 'previousSets', 'suggestedYear'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', TariffSet::class);
        $data = $request->validate([
            'village_id'     => ['required', 'exists:villages,id'],
            'jalali_year'    => ['required', 'integer', 'min:1404', 'max:1450'],
            'copy_from_id'   => ['nullable', 'exists:tariff_sets,id'],
            'inflation_rate' => ['required', 'numeric', 'min:0', 'max:200'],
        ]);
        $village = Village::findOrFail($data['village_id']);
        $copyFrom = $data['copy_from_id'] ? TariffSet::find($data['copy_from_id']) : null;
        try {
            $set = $this->service->createForVillage($village, (int) $data['jalali_year'], $copyFrom, $request->user());
            if ((float) $data['inflation_rate'] !== (float) $set->inflation_rate) {
                $set->update(['inflation_rate' => $data['inflation_rate']]);
            }
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['village_id' => $e->getMessage()]);
        }
        return redirect()->route('admin.tariffs.edit', $set)->with('success', 'مجموعه تعرفه ساخته شد.');
    }

    public function show(TariffSet $tariffSet)
    {
        Gate::authorize('view', $tariffSet);
        $tariffSet->load(['village', 'items.title', 'parameters', 'creator']);
        $inflationCheck = $this->service->validateInflation($tariffSet);
        return view('admin.tariffs.show', compact('tariffSet', 'inflationCheck'));
    }

    public function edit(TariffSet $tariffSet)
    {
        Gate::authorize('update', $tariffSet);
        $tariffSet->load(['village', 'items.title', 'parameters']);
        $titles = TariffTitle::active()->orderBy('sort_order')->get()->groupBy('type');
        return view('admin.tariffs.edit', compact('tariffSet', 'titles'));
    }

    public function update(Request $request, TariffSet $tariffSet)
    {
        return $this->updateItems($request, $tariffSet);
    }

    public function updateItems(Request $request, TariffSet $tariffSet)
    {
        Gate::authorize('update', $tariffSet);
        $data = $request->validate([
            'items'                    => ['array'],
            'items.*.id'               => ['required', 'exists:tariff_items,id'],
            'items.*.is_active'        => ['boolean'],
            'items.*.increase_percent' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'items.*.final_amount'     => ['nullable', 'integer', 'min:0'],
            'items.*.formula_display'  => ['nullable', 'string', 'max:500'],
            'items.*.notes'            => ['nullable', 'string', 'max:2000'],
            'parameters'               => ['array'],
            'parameters.*.key'         => ['required', 'string'],
            'parameters.*.value'       => ['nullable'],
        ]);
        DB::transaction(function () use ($tariffSet, $data) {
            foreach ($data['items'] ?? [] as $row) {
                $item = $tariffSet->items()->find($row['id']);
                if (!$item) continue;
                $item->update([
                    'is_active'        => (bool) ($row['is_active'] ?? false),
                    'increase_percent' => $row['increase_percent'] ?? $item->increase_percent,
                    'final_amount'     => $row['final_amount'] ?? $item->final_amount,
                    'formula_display'  => $row['formula_display'] ?? null,
                    'notes'            => $row['notes'] ?? null,
                ]);
            }
            foreach ($data['parameters'] ?? [] as $p) {
                if (empty($p['key'])) continue;
                TariffParameter::put($tariffSet, $p['key'], $p['value']);
            }
        });
        return back()->with('success', 'تغییرات ذخیره شد.');
    }

    public function transition(Request $request, TariffSet $tariffSet, string $to)
    {
        Gate::authorize('transition', [$tariffSet, $to]);
        try {
            match ($to) {
                'proposed'                  => $this->service->propose($tariffSet),
                'village_council_approved'  => $this->service->approveByVillageCouncil($tariffSet),
                'district_council_approved' => $this->service->approveByDistrictCouncil($tariffSet),
                'finalized'                 => $this->service->finalize($tariffSet),
                'published'                 => $this->service->publish($tariffSet),
                default => throw new InvalidArgumentException("transition نامعتبر: {$to}"),
            };
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['transition' => $e->getMessage()]);
        }
        return back()->with('success', 'وضعیت: ' . $tariffSet->fresh()->statusLabel());
    }

    public function destroy(TariffSet $tariffSet)
    {
        Gate::authorize('delete', $tariffSet);
        $tariffSet->delete();
        return redirect()->route('admin.tariffs.index')->with('success', 'حذف شد.');
    }

    private function availableVillages($user)
    {
        $q = Village::orderBy('name');
        if ($user && method_exists($user, 'isVillageAdmin') && $user->isVillageAdmin() && $user->village_id) {
            $q->where('id', $user->village_id);
        } elseif ($user && method_exists($user, 'isSectionAdmin') && $user->isSectionAdmin() && ($user->section_id ?? null)) {
            $q->where('section_id', $user->section_id);
        }
        return $q->get();
    }

    /**
     * بررسی اینکه کاربر از مسیر /tariff-officer/login آمده باشد.
     * super_admin از این قاعده مستثنا است.
     */
    private function hasTariffOfficerMode(Request $request): bool
    {
        if (!auth()->check()) {
            return false;
        }

        // flag اصلی
        $mode  = $request->session()->get('tariff_officer_mode') === true;

        // اعتبار زمانی (۸ ساعت)
        $until = (int) $request->session()->get('tariff_officer_until', 0);
        $valid = $until > 0 && now()->timestamp <= $until;

        // IP یکسان (جلوگیری از hijack)
        $ip    = $request->session()->get('tariff_officer_ip');
        $ipOk  = !$ip || $ip === $request->ip();

        if (!$mode || !$valid || !$ipOk) {
            $request->session()->forget([
                'tariff_officer_mode',
                'tariff_officer_until',
                'tariff_officer_ip',
            ]);
            return false;
        }

        return true;
    }

}
