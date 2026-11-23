<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Morilog\Jalali\Jalalian;
use App\Models\BankAccount;
use App\Models\LevyPublicRequest;
use App\Models\LevyVillageParam;
use App\Models\TariffTitle;
use App\Models\Village;
use App\Services\LevyCalculator;
use App\Support\Number;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * صفحهٔ عمومی عوارض روستا (جایگزین ماژول بایگانی‌شدهٔ tariff_*)
 *
 *  • عوارض احداث بنا: کاربر متراژ وارد میکند، مبلغ دقیقاً طبق فرمول اکسل محاسبه میشود.
 *  • عوارض پسماند: مبالغ توسط کارشناس عمرانی در ماژول works-fees وارد شده و سراسری است.
 *  • پرداخت: نمایش حساب دهیاری + آپلود رسید توسط کاربر + تأیید کارشناس.
 */
class PublicLevyController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // کمککنندهها
    // ═══════════════════════════════════════════════════════════

    private function village(string $slug): Village
    {
        return Village::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    private function params(Village $village): ?LevyVillageParam
    {
        return LevyVillageParam::where('village_id', $village->id)->first();
    }

    /** حساب تعریفشدهٔ دهیاری؛ در نبود آن از اطلاعات حساب روستا استفاده میشود */
    private function accountFor(Village $village): array
    {
        $account = BankAccount::where('village_id', $village->id)
            ->where('is_active', 1)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();

        if ($account) {
            return [
                'bank'   => $account->bank_name ?: 'بانک',
                'holder' => $account->account_holder ?: ('دهیاری روستای ' . $village->name),
                'card'   => $account->card_number,
                'number' => $account->account_number,
                'iban'   => $account->iban,
            ];
        }

        return [
            'bank'   => $village->bank_name ?: 'بانک',
            'holder' => 'دهیاری روستای ' . $village->name,
            'card'   => null,
            'number' => $village->bank_account,
            'iban'   => $village->bank_iban,
        ];
    }

    private function wasteTitles()
    {
        return TariffTitle::where('category', 'waste')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get(['id', 'name_fa', 'unit', 'base_amount', 'description']);
    }

    private function normalizeMobile(?string $mobile): string
    {
        $mobile = Number::en($mobile);
        return preg_replace('/[^0-9]/', '', (string) $mobile) ?? '';
    }

    // ═══════════════════════════════════════════════════════════
    // صفحهٔ اصلی و ماشینحساب
    // ═══════════════════════════════════════════════════════════

    public function index(Request $request, string $slug)
    {
        $village = $this->village($slug);

        return view('village.levy.index', [
            'village'     => $village,
            'params'      => $this->params($village),
            'wasteTitles' => $this->wasteTitles(),
            'account'     => $this->accountFor($village),
        ]);
    }

    /** محاسبهٔ زنده (AJAX) — بدون ذخیرهٔ هیچ رکوردی */
    public function calculate(Request $request, string $slug): JsonResponse
    {
        $village = $this->village($slug);

        $data = $request->validate([
            'type'            => ['required', 'in:construction,waste'],
            'construction_area' => ['required_if:type,construction', 'nullable', 'numeric', 'min:0'],
            'property_area'   => ['nullable', 'numeric', 'min:0'],
            'items'           => ['required_if:type,waste', 'nullable', 'array'],
            'items.*.id'      => ['required_with:items', 'integer', 'exists:tariff_titles,id'],
            'items.*.qty'     => ['required_with:items', 'integer', 'min:1', 'max:100000'],
            'items.*.enabled' => ['nullable', 'in:1'],
        ], [
            'construction_area.required_if' => 'مساحت ساخت را وارد کنید.',
            'construction_area.numeric'     => 'مساحت ساخت باید عدد باشد.',
            'items.required_if'             => 'حداقل یک قلم پسماند را انتخاب کنید.',
        ]);

        if ($data['type'] === 'construction') {
            $calc = LevyCalculator::construction(
                $this->params($village),
                (float) Number::en($data['construction_area'] ?? 0),
                (float) Number::en($data['property_area'] ?? 0)
            );

            return response()->json([
                'ok'     => true,
                'type'   => 'construction',
                'calc'   => $calc,
                'amount' => $calc['final_levy'],
                'money'  => Number::money($calc['final_levy']),
            ]);
        }

        $total = 0;
        $lines = [];
        foreach ($data['items'] ?? [] as $row) {
            if (empty($row['enabled'])) {
                continue;
            }
            $title = TariffTitle::where('id', $row['id'])
                ->where('category', 'waste')->where('is_active', 1)->first();
            if (!$title) {
                continue;
            }
            $qty = (int) $row['qty'];
            $price = (int) $title->base_amount;
            $total += $price * $qty;
            $lines[] = [
                'id' => $title->id, 'title' => $title->name_fa,
                'unit' => $title->unit, 'price' => $price,
                'qty' => $qty, 'amount' => $price * $qty,
            ];
        }

        return response()->json([
            'ok'     => true,
            'type'   => 'waste',
            'lines'  => $lines,
            'amount' => $total,
            'money'  => Number::money($total),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // ثبت درخواست
    // ═══════════════════════════════════════════════════════════

    public function store(Request $request, string $slug)
    {
        $village = $this->village($slug);

        $data = $request->validate([
            'type'              => ['required', 'in:construction,waste'],
            'payer_name'        => ['required', 'string', 'max:200'],
            'payer_mobile'      => ['required', 'string', 'max:20'],
            'national_id'       => ['nullable', 'string', 'max:20'],
            'notes'             => ['nullable', 'string', 'max:1000'],
            'construction_area' => ['required_if:type,construction', 'nullable', 'numeric', 'min:0'],
            'property_area'     => ['nullable', 'numeric', 'min:0'],
            'items'             => ['required_if:type,waste', 'nullable', 'array'],
            'items.*.id'        => ['required_with:items', 'integer', 'exists:tariff_titles,id'],
            'items.*.qty'       => ['required_with:items', 'integer', 'min:1', 'max:100000'],
            'items.*.enabled'   => ['nullable', 'in:1'],
        ], [
            'type.required'                 => 'نوع عوارض را مشخص کنید.',
            'payer_name.required'           => 'نام و نام خانوادگی را وارد کنید.',
            'payer_mobile.required'         => 'شماره تلفن را وارد کنید.',
            'construction_area.required_if' => 'مساحت ساخت را وارد کنید.',
            'items.required_if'             => 'حداقل یک قلم پسماند را انتخاب کنید.',
        ]);

        $account = $this->accountFor($village);
        $isConstruction = $data['type'] === 'construction';
        $calc = null;
        $lines = [];
        $amount = 0;

        if ($isConstruction) {
            // محاسبهٔ سمت سرور — به عدد ارسالشده توسط مرورگر اعتماد نمیشود
            $calc = LevyCalculator::construction(
                $this->params($village),
                (float) Number::en($data['construction_area'] ?? 0),
                (float) Number::en($data['property_area'] ?? 0)
            );
            $amount = $calc['final_levy'];
        } else {
            foreach ($data['items'] ?? [] as $row) {
                if (empty($row['enabled'])) {
                    continue;
                }
                $title = TariffTitle::where('id', $row['id'])
                    ->where('category', 'waste')->where('is_active', 1)->first();
                if (!$title) {
                    continue;
                }
                $qty = (int) $row['qty'];
                $price = (int) $title->base_amount;
                $lines[] = [
                    'title' => $title, 'qty' => $qty, 'price' => $price, 'amount' => $price * $qty,
                ];
                $amount += $price * $qty;
            }

            if (empty($lines)) {
                return back()->withInput()->with('error', 'هیچ قلم فعال پسماندی انتخاب نشد.');
            }
        }

        $requestRow = DB::transaction(function () use (
            $village, $data, $account, $isConstruction, $calc, $lines, $amount
        ) {
            $row = LevyPublicRequest::create([
                'tracking_code'     => LevyPublicRequest::generateTrackingCode(),
                'village_id'        => $village->id,
                'type'              => $data['type'],
                'payer_name'        => $data['payer_name'],
                'payer_mobile'      => $this->normalizeMobile($data['payer_mobile']),
                'national_id'       => $data['national_id'] ?? null,
                'notes'             => $data['notes'] ?? null,
                'construction_area' => $isConstruction ? ($calc['construction_area'] ?? null) : null,
                'property_area'     => $isConstruction ? ($calc['property_area'] ?? null) : null,
                'transaction_value' => $calc['transaction_value'] ?? 0,
                'final_coefficient' => $calc['final_coefficient'] ?? 0,
                'residential_levy'  => $calc['residential_levy'] ?? 0,
                'density_levy'      => $calc['density_levy'] ?? 0,
                'final_levy'        => $calc['final_levy'] ?? 0,
                'amount'            => $amount,
                'account_no'        => $account['number'],
                'iban'              => $account['iban'],
                'account_holder'    => $account['holder'],
                'status'            => 'awaiting_receipt',
            ]);

            foreach ($lines as $i => $line) {
                $row->items()->create([
                    'tariff_title_id' => $line['title']->id,
                    'title_snapshot'  => $line['title']->name_fa,
                    'unit'            => $line['title']->unit,
                    'unit_price'      => $line['price'],
                    'quantity'        => $line['qty'],
                    'amount'          => $line['amount'],
                    'sort_order'      => $i + 1,
                ]);
            }

            $row->addEvent('created', 'ثبت درخواست توسط متقاضی');

            return $row;
        });

        return redirect()
            ->route('village.tariff.invoice.show', $requestRow->tracking_code)
            ->with('success', 'درخواست شما ثبت شد. لطفاً مبلغ را به حساب اعلامشده واریز و رسید آن را آپلود کنید.');
    }

    // ═══════════════════════════════════════════════════════════
    // پیگیری، نمایش و آپلود رسید
    // ═══════════════════════════════════════════════════════════

    public function trackForm(Request $request, string $slug)
    {
        $village = $this->village($slug);

        return view('village.levy.track', ['village' => $village]);
    }

    public function track(Request $request, string $slug)
    {
        $village = $this->village($slug);

        $data = $request->validate([
            'tracking_code' => ['required', 'string', 'max:32'],
            'payer_mobile'  => ['required', 'string', 'max:20'],
        ], [
            'tracking_code.required' => 'کد پیگیری را وارد کنید.',
            'payer_mobile.required'  => 'شماره تلفن ثبتشده را وارد کنید.',
        ]);

        $row = LevyPublicRequest::where('village_id', $village->id)
            ->where('tracking_code', strtoupper(trim($data['tracking_code'])))
            ->where('payer_mobile', $this->normalizeMobile($data['payer_mobile']))
            ->first();

        if (!$row) {
            return back()->withInput()->with('error', 'درخواستی با این کد پیگیری و شماره تلفن یافت نشد.');
        }

        return redirect()->route('village.tariff.invoice.show', $row->tracking_code);
    }

    public function show(string $token)
    {
        $row = LevyPublicRequest::where('tracking_code', $token)->with('village', 'items', 'events')->firstOrFail();

        return view('village.levy.show', ['requestRow' => $row, 'village' => $row->village]);
    }

    /**
     * رسید/فیش پرداخت قابل دانلود — برای عوارض احداث بنا و پسماند.
     * دسترسی صرفاً با کد پیگیری است (مانند صفحهٔ جزئیات درخواست).
     */
    public function receiptPdf(string $token)
    {
        $row = LevyPublicRequest::where('tracking_code', $token)
            ->with('village', 'items')
            ->firstOrFail();

        abort_unless($row->village && $row->village->is_active, 404);

        $pdf = Pdf::loadView('village.levy.receipt-pdf', [
            'requestRow'  => $row,
            'village'     => $row->village,
            'account'     => $this->accountFor($row->village),
            'amountWords' => $this->amountInWords((int) $row->amount),
            'issuedAt'    => Jalalian::fromCarbon($row->created_at)->format('Y/m/d'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('levy-receipt-' . $row->tracking_code . '.pdf');
    }

    public function receipt(Request $request, string $token)
    {
        $row = LevyPublicRequest::where('tracking_code', $token)->firstOrFail();

        if ($row->status === 'approved') {
            return back()->with('info', 'این درخواست قبلاً تأیید شده است.');
        }

        $data = $request->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'receipt.required' => 'فایل رسید پرداخت را انتخاب کنید.',
            'receipt.mimes'    => 'رسید باید با فرمت JPG، PNG یا PDF باشد.',
            'receipt.max'      => 'حجم رسید نباید بیش از ۵ مگابایت باشد.',
        ]);

        $path = $request->file('receipt')->store('levy-receipts', 'public');

        if ($row->receipt_path && Storage::disk('public')->exists($row->receipt_path)) {
            Storage::disk('public')->delete($row->receipt_path);
        }

        $row->update([
            'receipt_path' => $path,
            'status'       => 'under_review',
        ]);
        $row->addEvent('receipt_uploaded', 'آپلود رسید پرداخت');

        return back()->with('success', 'رسید پرداخت ارسال شد و در وضعیت بررسی قرار گرفت.');
    }

    // ═══════════════════════════════════════════════════════════
    // تبدیل مبلغ به حروف فارسی (صرفاً جهت درج در رسید پرداخت)
    // ═══════════════════════════════════════════════════════════

    private function amountInWords(int $number): string
    {
        if ($number <= 0) {
            return 'صفر ریال';
        }

        $ones = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
        $teens = ['ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'];
        $tens = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];
        $hundreds = ['', 'یکصد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];

        $parts = [];
        foreach ([[1000000000, 'میلیارد'], [1000000, 'میلیون'], [1000, 'هزار'], [1, '']] as [$unit, $label]) {
            if ($number < $unit) {
                continue;
            }
            $value = intdiv($number, $unit);
            $number %= $unit;
            $words = $this->underThousandWords($value, $ones, $teens, $tens, $hundreds);
            if ($words !== '') {
                $parts[] = trim($words . ' ' . $label);
            }
        }

        return implode(' و ', $parts) . ' ریال';
    }

    private function underThousandWords(
        int $number,
        array $ones,
        array $teens,
        array $tens,
        array $hundreds
    ): string {
        $parts = [];

        if ($number >= 100) {
            $parts[] = $hundreds[intdiv($number, 100)];
            $number %= 100;
        }

        if ($number >= 10 && $number < 20) {
            $parts[] = $teens[$number - 10];
        } else {
            if ($number >= 20) {
                $parts[] = $tens[intdiv($number, 10)];
                $number %= 10;
            }
            if ($number > 0) {
                $parts[] = $ones[$number];
            }
        }

        return implode(' و ', $parts);
    }
}
