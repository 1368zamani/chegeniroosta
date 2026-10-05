<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TariffItem;
use App\Models\Village;
use App\Services\Tariff\Calculators\CalculatorRegistry;
use Illuminate\Http\Request;

class TariffCalculatorController extends Controller
{
    public function __construct(private CalculatorRegistry $registry) {}

    public function calculate(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'item_id' => ['required', 'integer', 'exists:tariff_items,id'],
            'inputs'  => ['array'],
        ]);

        $item = TariffItem::with('title', 'set')->findOrFail($data['item_id']);
        $set  = $item->set;

        // امنیت: فقط آیتم‌های یک مجموعه‌ی منتشرشده‌ی همین روستا
        if (!$set || (int) $set->village_id !== (int) $village->id || $set->status !== 'published') {
            abort(404);
        }
        if (!$item->is_active) {
            return response()->json(['ok' => false, 'error' => 'این عنوان فعال نیست.'], 422);
        }

        $key = $item->title?->formula_key;
        if (!$key || !$this->registry->has($key)) {
            return response()->json(['ok' => false, 'error' => 'ماشین‌حساب برای این عنوان تعریف نشده.'], 422);
        }

        try {
            $result = $this->registry->get($key)->calculate($set, $item, $data['inputs'] ?? []);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }

        return response()->json([
            'ok'        => true,
            'amount'    => $result->amount,
            'breakdown' => $result->breakdown,
            'warnings'  => $result->warnings,
        ]);
    }
}
