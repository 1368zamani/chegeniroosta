<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TariffItem;
use App\Models\TariffSet;
use App\Models\Village;
use App\Services\Tariff\TariffCalculatorService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class TariffCalculatorController extends Controller
{
    public function __construct(private TariffCalculatorService $service) {}

    /**
     * API: schema ورودی‌های یک عنوان.
     */
    public function schema(int $itemId)
    {
        $item = TariffItem::with('title')->findOrFail($itemId);
        $schema = $this->service->inputSchema($item);

        return response()->json([
            'ok' => true,
            'title' => $item->title?->name_fa,
            'calculable' => $this->service->isCalculable($item),
            'schema' => $schema,
        ]);
    }

    /**
     * API: محاسبه.
     */
    public function calculate(Request $request, int $itemId)
    {
        $item = TariffItem::with(['title', 'set.parameters'])->findOrFail($itemId);

        if (!$item->set || $item->set->status !== 'published') {
            return response()->json(['ok' => false, 'error' => 'این تعرفه در دسترس عموم نیست.'], 403);
        }

        try {
            $result = $this->service->calculateItem($item, $request->input('inputs', []));

            return response()->json([
                'ok' => true,
                'total' => $result->total,
                'total_formatted' => number_format($result->total),
                'steps' => $result->steps,
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'خطا در محاسبه.'], 500);
        }
    }
}
