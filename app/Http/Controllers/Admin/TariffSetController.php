<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffItem;
use App\Models\TariffSet;
use App\Models\TariffTitle;
use App\Models\Village;
use App\Services\Tariff\TariffSetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TariffSetController extends Controller
{
    public function __construct(private TariffSetService $service) {}

    /**
     * لیست مجموعه‌های تعرفه در دسترس کاربر بر اساس نقش.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = TariffSet::with(['village.section'])->orderByDesc('jalali_year');

        if ($user->isSectionAdmin()) {
            $villageIds = Village::where('section_id', $user->section_id)->pluck('id');
            $query->whereIn('village_id', $villageIds);
        } elseif ($user->isVillageAdmin()) {
            $query->where('village_id', $user->village_id);
        } elseif ($user->isFeeOfficer()) {
            $villageIds = Village::where('section_id', $user->section_id)->pluck('id');
            $query->whereIn('village_id', $villageIds);
        }

        if ($request->filled('year')) {
            $query->where('jalali_year', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('village', fn($w) => $w->where('name', 'like', "%$q%"));
        }

        $sets = $query->paginate(20)->withQueryString();
        $years = TariffSet::distinct()->orderByDesc('jalali_year')->pluck('jalali_year');

        return view('admin.tariffs.index', compact('sets', 'years'));
    }

    /**
     * فرم ساخت مجموعه جدید.
     */
    public function create()
    {
        $user = auth()->user();

        // روستاهای قابل دسترس
        $villages = match (true) {
            $user->isSuperAdmin()  => Village::orderBy('name')->get(),
            $user->isSectionAdmin() => Village::where('section_id', $user->section_id)->orderBy('name')->get(),
            $user->isVillageAdmin() => Village::where('id', $user->village_id)->get(),
            default                => collect(),
        };

        // سال‌های موجود برای کپی
        $previousSets = TariffSet::with('village')
            ->orderByDesc('jalali_year')
            ->get()
            ->groupBy('village_id');

        $suggestedYear = (int) (\Morilog\Jalali\Jalalian::now()->getYear() + 1);

        return view('admin.tariffs.create', compact('villages', 'previousSets', 'suggestedYear'));
    }

    /**
     * ذخیره مجموعه جدید (با امکان کپی از سال قبل).
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'village_id'  => 'required|exists:villages,id',
            'jalali_year' => 'required|integer|min:1400|max:1450',
            'copy_from'   => 'nullable|exists:tariff_sets,id',
            'inflation_rate' => 'nullable|numeric|min:0|max:200',
        ], [
            'village_id.required'  => 'انتخاب روستا الزامی است.',
            'jalali_year.required' => 'سال شمسی الزامی است.',
        ]);

        $village = Village::findOrFail($data['village_id']);

        // دسترسی
        if (!$user->isSuperAdmin()) {
            if ($user->isSectionAdmin() && $village->section_id !== $user->section_id) {
                abort(403, 'این روستا در بخش شما نیست.');
            }
            if ($user->isVillageAdmin() && $village->id !== $user->village_id) {
                abort(403, 'فقط می‌توانید برای روستای خود تعرفه بسازید.');
            }
        }

        $copyFrom = null;
        if (!empty($data['copy_from'])) {
            $copyFrom = TariffSet::find($data['copy_from']);
            if ($copyFrom && $copyFrom->village_id !== $village->id) {
                return back()->withErrors(['copy_from' => 'مجموعه مبدأ باید برای همین روستا باشد.'])->withInput();
            }
        }

        try {
            $set = $this->service->createForVillage($village, (int) $data['jalali_year'], $copyFrom, $user);

            // اگر کاربر نرخ تورم را دستی داده
            if (!empty($data['inflation_rate'])) {
                $set->update(['inflation_rate' => $data['inflation_rate']]);
            }

            return redirect()
                ->route('admin.tariffs.show', $set->id)
                ->with('success', '✅ مجموعه تعرفه با موفقیت ساخته شد.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['jalali_year' => $e->getMessage()])->withInput();
        }
    }

    /**
     * نمایش یک مجموعه با همه آیتم‌ها.
     */
    public function show(int $id)
    {
        $set = TariffSet::with(['village.section', 'items.title', 'parameters', 'creator'])
            ->findOrFail($id);

        $this->authorize('view', $set);

        // مقایسه با سال قبل
        $previousSet = TariffSet::where('village_id', $set->village_id)
            ->where('jalali_year', '<', $set->jalali_year)
            ->orderByDesc('jalali_year')
            ->first();

        $validation = $this->service->validateInflation($set);

        // گروه‌بندی آیتم‌ها
        $levyItems     = $set->items->filter(fn($i) => $i->title?->type === 'levy');
        $serviceItems  = $set->items->filter(fn($i) => $i->title?->type === 'service_fee');
        $penaltyItems  = $set->items->filter(fn($i) => $i->title?->type === 'penalty');

        return view('admin.tariffs.show', compact(
            'set', 'previousSet', 'validation',
            'levyItems', 'serviceItems', 'penaltyItems'
        ));
    }

    /**
     * فرم ویرایش (فقط draft).
     */
    public function edit(int $id)
    {
        $set = TariffSet::with(['village.section', 'items.title', 'parameters'])->findOrFail($id);

        $this->authorize('update', $set);

        $levyItems    = $set->items->filter(fn($i) => $i->title?->type === 'levy')
            ->sortBy(fn($i) => $i->title?->sort_order);
        $serviceItems = $set->items->filter(fn($i) => $i->title?->type === 'service_fee')
            ->sortBy(fn($i) => $i->title?->sort_order);

        return view('admin.tariffs.edit', compact('set', 'levyItems', 'serviceItems'));
    }

    /**
     * ذخیره ویرایش‌ها.
     */
    public function update(Request $request, int $id)
    {
        $set = TariffSet::findOrFail($id);
        $this->authorize('update', $set);

        $data = $request->validate([
            'inflation_rate' => 'required|numeric|min:0|max:200',
            'items'          => 'required|array',
            'items.*.id'     => 'required|integer|exists:tariff_items,id',
            'items.*.is_active'        => 'nullable|boolean',
            'items.*.previous_amount'  => 'nullable|integer|min:0',
            'items.*.increase_percent' => 'nullable|numeric|min:0|max:500',
            'items.*.final_amount'     => 'nullable|integer|min:0',
            'items.*.notes'            => 'nullable|string|max:500',
            'items.*.unit'             => 'nullable|string|max:30',
        ], [
            'inflation_rate.required' => 'نرخ تورم الزامی است.',
        ]);

        $set->update(['inflation_rate' => $data['inflation_rate']]);

        foreach ($data['items'] as $row) {
            $item = TariffItem::where('tariff_set_id', $set->id)->find($row['id']);
            if (!$item) continue;

            $final = $row['final_amount'] ?? null;
            if ($final === null) {
                // اگر مبلغ نهایی خالی بود، از previous + increase محاسبه کن
                $prev = (int) ($row['previous_amount'] ?? $item->previous_amount);
                $inc  = (float) ($row['increase_percent'] ?? $item->increase_percent);
                $final = $this->service->applyIncrease($prev, $inc);
            }

            $item->update([
                'is_active'        => isset($row['is_active']) && $row['is_active'] ? true : false,
                'previous_amount'  => (int) ($row['previous_amount'] ?? $item->previous_amount),
                'increase_percent' => (float) ($row['increase_percent'] ?? $item->increase_percent),
                'final_amount'     => (int) $final,
                'notes'            => $row['notes'] ?? $item->notes,
                'unit'             => $row['unit'] ?? $item->unit,
            ]);
        }

        return redirect()
            ->route('admin.tariffs.show', $set->id)
            ->with('success', '✅ مجموعه تعرفه به‌روزرسانی شد.');
    }

    /**
     * تغییر وضعیت workflow.
     */
    public function transition(Request $request, int $id)
    {
        $set = TariffSet::findOrFail($id);

        $data = $request->validate([
            'to' => 'required|in:proposed,village_council_approved,district_council_approved,finalized,published',
        ]);

        $this->authorize('transition', [$set, $data['to']]);

        try {
            match ($data['to']) {
                'proposed'                  => $this->service->propose($set),
                'village_council_approved'  => $this->service->approveByVillageCouncil($set),
                'district_council_approved' => $this->service->approveByDistrictCouncil($set),
                'finalized'                 => $this->service->finalize($set),
                'published'                 => $this->service->publish($set),
            };

            return back()->with('success', '✅ وضعیت به «' . $set->fresh()->statusLabel() . '» تغییر کرد.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['transition' => $e->getMessage()]);
        }
    }

    /**
     * آپلود PDF مصوبه شورا.
     */
    public function uploadApproval(Request $request, int $id)
    {
        $set = TariffSet::findOrFail($id);
        $this->authorize('update', $set);

        $request->validate([
            'approval_document' => 'required|file|mimes:pdf|max:10240',
        ], [
            'approval_document.required' => 'انتخاب فایل الزامی است.',
            'approval_document.mimes'    => 'فایل باید PDF باشد.',
            'approval_document.max'      => 'حجم فایل نباید بیشتر از ۱۰ مگابایت باشد.',
        ]);

        if ($set->approval_document_path && Storage::disk('public')->exists($set->approval_document_path)) {
            Storage::disk('public')->delete($set->approval_document_path);
        }

        $path = $request->file('approval_document')->store('tariff-approvals', 'public');
        $set->update(['approval_document_path' => $path]);

        return back()->with('success', '✅ فایل مصوبه آپلود شد.');
    }

    /**
     * حذف فایل مصوبه.
     */
    public function deleteApproval(int $id)
    {
        $set = TariffSet::findOrFail($id);
        $this->authorize('update', $set);

        if ($set->approval_document_path && Storage::disk('public')->exists($set->approval_document_path)) {
            Storage::disk('public')->delete($set->approval_document_path);
        }

        $set->update(['approval_document_path' => null]);

        return back()->with('success', '✅ فایل مصوبه حذف شد.');
    }

    /**
     * دانلود فایل مصوبه.
     */
    public function downloadApproval(int $id)
    {
        $set = TariffSet::findOrFail($id);
        $this->authorize('view', $set);

        if (!$set->approval_document_path || !Storage::disk('public')->exists($set->approval_document_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($set->approval_document_path);
    }

    /**
     * حذف مجموعه (فقط draft).
     */
    public function destroy(int $id)
    {
        $set = TariffSet::findOrFail($id);
        $this->authorize('delete', $set);

        if ($set->approval_document_path && Storage::disk('public')->exists($set->approval_document_path)) {
            Storage::disk('public')->delete($set->approval_document_path);
        }

        $set->delete();

        return redirect()
            ->route('admin.tariffs.index')
            ->with('success', '✅ مجموعه تعرفه حذف شد.');
    }
}
