<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Dehyari;
use App\Models\FeeCategory;
use App\Models\FeeTariff;
use App\Http\Requests\CalculateFeeRequest;
use App\Services\FeeCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * کنترلر عمومی محاسبه عوارض و صدور فاکتور (قبض).
 * کاربر بدون لاگین می‌تواند محاسبه کند و قبض بگیرد.
 */
class FeeCalculatorController extends Controller
{
    public function __construct(private FeeCalculator $calculator) {}

    /** صفحه اصلی محاسبه‌گر: لیست دسته‌ها و انواع */
    public function index()
    {
        $categories = FeeCategory::active()
            ->with(['feeTypes' => fn ($q) => $q->active()->with('parameters')])
            ->orderBy('sort_order')
            ->get();

        $dehyaris = Dehyari::where('is_active', true)->orderBy('name')->get();

        return view('fees.calculator', compact('categories', 'dehyaris'));
    }

    /** دریافت فرم، محاسبه، ذخیره قبض، انتقال به صفحه قبض */
    public function calculate(CalculateFeeRequest $request)
    {
        $data = $request->validated();
        $data['inputs'] = $data['inputs'] ?? [];

        $tariff = FeeTariff::where('dehyari_id', $data['dehyari_id'])
            ->where('fee_type_id', $data['fee_type_id'])
            ->where('is_active', true)
            ->latest('id')
            ->first();

        if (!$tariff) {
            return back()->withErrors(['fee_type_id' => 'برای این نوع عوارض، تعرفه فعالی ثبت نشده است.'])->withInput();
        }

        $requiredParams = $tariff->feeType->parameters()->where('is_required', true)->pluck('key')->all();
        foreach ($requiredParams as $key) {
            if (!array_key_exists($key, $data['inputs']) || $data['inputs'][$key] === '' || $data['inputs'][$key] === null) {
                return back()->withErrors(['inputs' => 'لطفاً همه فیلدهای الزامی را پر کنید.'])->withInput();
            }
        }

        try {
            $result = $this->calculator->calculate($tariff, $data['inputs']);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['inputs' => $e->getMessage()])->withInput();
        }

        $bill = Bill::create([
            'dehyari_id'       => $data['dehyari_id'],
            'fee_tariff_id'    => $tariff->id,
            'bill_number'      => $this->generateBillNumber(),
            'payment_id'       => $this->generatePaymentId(),
            'taxpayer_name'    => $data['taxpayer_name'],
            'national_code'    => $data['national_code'] ?? null,
            'mobile'           => $data['mobile'],
            'address'          => $data['address'] ?? null,
            'fee_category_name'=> $result['snapshot']['category_name'] ?? '-',
            'fee_type_name'    => $result['snapshot']['fee_type_name'] ?? '-',
            'year'             => $tariff->year,
            'unit'             => $tariff->unit,
            'formula_type'     => $tariff->formula_type,
            'formula_display'  => $result['snapshot']['formula_display'],
            'parameters_snapshot' => $data['inputs'],
            'calculation_steps'   => $result['steps'],
            'amount_rial'      => $result['total'],
            'issue_date'       => now()->toDateString(),
            'due_date'         => now()->addDays(30)->toDateString(),
            'status'           => 'unpaid',
        ]);

        foreach ($result['steps'] as $i => $step) {
            BillItem::create([
                'bill_id'    => $bill->id,
                'title'      => $step['label'],
                'description'=> $step['detail'] ?? null,
                'amount_rial'=> $step['amount'] ?? 0,
                'sort_order' => $i,
            ]);
        }

        return redirect()->route('fees.bill.show', $bill->bill_number);
    }

    /** نمایش قبض با کد پیگیری و اطلاعات واریز */
    public function show(string $billNumber)
    {
        $bill = Bill::where('bill_number', $billNumber)->firstOrFail();
        $items = $bill->items()->orderBy('sort_order')->get();
        $accounts = \App\Models\PaymentAccount::where('dehyari_id', $bill->dehyari_id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->get();

        return view('fees.bill', compact('bill', 'items', 'accounts'));
    }

    private function generateBillNumber(): string
    {
        do {
            $n = 'BN' . now()->format('ymd') . strtoupper(Str::random(5));
        } while (Bill::where('bill_number', $n)->exists());
        return $n;
    }

    private function generatePaymentId(): string
    {
        do {
            $n = 'PY' . now()->format('ymd') . strtoupper(Str::random(5));
        } while (Bill::where('payment_id', $n)->exists());
        return $n;
    }
}
