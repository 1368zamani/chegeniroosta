<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TariffItem;
use App\Models\TariffSet;
use App\Models\TariffParameter;
use App\Models\Village;
use App\Services\Tariff\Calculators\CalculatorRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TariffEditController extends Controller
{
    public function __construct(private CalculatorRegistry $registry) {}

    /**
     * محاسبه بدون ذخیره
     */
    public function calculate(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'item_id' => ['required', 'integer', 'exists:tariff_items,id'],
            'inputs'  => ['array'],
        ]);

        $item = TariffItem::with('set', 'title')->findOrFail($data['item_id']);
        $set  = $item->set;

        if (!$set || (int) $set->village_id !== (int) $village->id || $set->status !== 'published') {
            return response()->json(['ok' => false, 'error' => 'تعرفه در دسترس نیست.'], 404);
        }

        $key = $item->title?->formula_key;
        if (!$key || !$this->registry->has($key)) {
            return response()->json(['ok' => false, 'error' => 'ماشین‌حساب تعریف نشده.'], 422);
        }

        try {
            $result = $this->registry->get($key)->calculate($set, $item, $data['inputs'] ?? []);
            return response()->json([
                'ok'        => true,
                'amount'    => $result->amount,
                'breakdown' => $result->breakdown,
                'warnings'  => $result->warnings,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * ⭐ محاسبه + ذخیره
     */
    public function update(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'item_id' => ['required', 'integer', 'exists:tariff_items,id'],
            'inputs'  => ['array'],
        ]);

        $item = TariffItem::with('set', 'title')->findOrFail($data['item_id']);
        $set  = $item->set;

        if (!$set || (int) $set->village_id !== (int) $village->id || $set->status !== 'published') {
            return response()->json(['ok' => false, 'error' => 'تعرفه در دسترس نیست.'], 404);
        }

        // بررسی دسترسی
        if (!Gate::forUser($request->user())->allows('updateItems', $set)) {
            return response()->json(['ok' => false, 'error' => 'دسترسی ندارید.'], 403);
        }

        // بررسی نوع تعرفه
        $type = $item->title?->type;
        if (!$type || !Gate::forUser($request->user())->allows('tariff.canEditType', $type)) {
            return response()->json([
                'ok' => false,
                'error' => 'شما مجاز به ویرایش این نوع تعرفه (' . $type . ') نیستید.',
            ], 403);
        }

        $key = $item->title?->formula_key;
        if (!$key || !$this->registry->has($key)) {
            return response()->json(['ok' => false, 'error' => 'ماشین‌حساب تعریف نشده.'], 422);
        }

        try {
            DB::beginTransaction();

            $oldAmount = $item->final_amount;
            $oldParams = $item->params;

            $result = $this->registry->get($key)->calculate($set, $item, $data['inputs'] ?? []);

            $item->update([
                'final_amount'     => $result->amount,
                'params'           => $result->meta['params'] ?? $item->params,
                'formula_display'  => $result->meta['formula_display'] ?? null,
                'increase_percent' => $result->meta['increase_percent'] ?? 0,
                'updated_at'       => now(),
            ]);

            $this->logChange($request->user(), $item, $oldAmount, $result->amount, $oldParams, $item->params);

            DB::commit();

            return response()->json([
                'ok'         => true,
                'amount'     => $result->amount,
                'breakdown'  => $result->breakdown,
                'old_amount' => $oldAmount,
                'message'    => 'ذخیره شد.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Tariff update failed', ['item_id' => $item->id, 'error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * تاریخچه تغییرات
     */
    public function history(Request $request, string $slug, int $item)
    {
        $village = Village::where('slug', $slug)->firstOrFail();
        $itemModel = TariffItem::with('set')->findOrFail($item);

        if (!$itemModel->set || (int) $itemModel->set->village_id !== (int) $village->id) {
            abort(404);
        }

        Gate::authorize('updateItems', $itemModel->set);

        $logs = DB::table('audit_logs')
            ->where('entity_type', TariffItem::class)
            ->where('entity_id', $itemModel->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $userIds = $logs->pluck('user_id')->unique()->filter()->toArray();
        $users = \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id');

        $history = $logs->map(function ($log) use ($users) {
            $old = json_decode($log->old_values, true) ?: [];
            $new = json_decode($log->new_values, true) ?: [];
            return [
                'id'         => $log->id,
                'user_name'  => $users[$log->user_id] ?? 'نامشخص',
                'old_amount' => $old['amount'] ?? 0,
                'new_amount' => $new['amount'] ?? 0,
                'date'       => \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($log->created_at))->format('Y/m/d H:i'),
            ];
        });

        return response()->json(['history' => $history]);
    }

    /**
     * ذخیره لاگ
     */
    private function logChange($user, TariffItem $item, $oldAmount, $newAmount, $oldParams, $newParams): void
    {
        try {
            DB::table('audit_logs')->insert([
                'user_id'     => $user?->id,
                'action'      => 'tariff.item.update',
                'entity_type' => TariffItem::class,
                'entity_id'   => $item->id,
                'old_values'  => json_encode(['amount' => $oldAmount, 'params' => $oldParams], JSON_UNESCAPED_UNICODE),
                'new_values'  => json_encode(['amount' => $newAmount, 'params' => $newParams], JSON_UNESCAPED_UNICODE),
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
                'created_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Audit log failed', ['error' => $e->getMessage()]);
        }
    }
}
