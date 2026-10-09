<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\TariffInvoice;
use App\Models\TariffInvoiceLine;
use App\Models\TariffItem;
use App\Models\TariffSet;
use App\Models\Village;
use App\Services\Tariff\Calculators\CalculatorRegistry;
use App\Support\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TariffInvoiceController extends Controller
{
    public function __construct(private CalculatorRegistry $registry) {}

    /**
     * فرم صدور فیش جدید
     * GET /village/{slug}/tariff/invoice/create
     */
    public function create(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // پیدا کردن تعرفه منتشرشده سال جاری
        $set = TariffSet::where('village_id', $village->id)
            ->where('status', 'published')
            ->orderByDesc('jalali_year')
            ->first();

        if (!$set) {
            return redirect()
                ->route('village.site.request.create', $slug)
                ->withErrors(['error' => 'تعرفه‌ای برای این روستا صادر نشده است.']);
        }

        // فقط آیتم‌های فعال
        $items = TariffItem::with('title')
            ->where('tariff_set_id', $set->id)
            ->where('is_active', true)
            ->whereHas('title')
            ->get()
            ->groupBy(fn($i) => $i->title->type);

        return view('public.tariff.invoice.create', compact('village', 'set', 'items'));
    }

    /**
     * صدور فیش جدید
     * POST /village/{slug}/tariff/invoice
     */
    public function store(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $data = $request->validate([
            'payer_name'        => ['required', 'string', 'max:200'],
            'payer_national_id' => ['nullable', 'string', 'max:20'],
            'payer_mobile'      => ['required', 'string', 'max:20'],
            'subject'           => ['nullable', 'string', 'max:500'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.item_id'   => ['required', 'integer', 'exists:tariff_items,id'],
            'items.*.amount'    => ['required', 'integer', 'min:0'],
            'items.*.discount'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.inputs'    => ['nullable', 'array'],
            'items.*.notes'     => ['nullable', 'string'],
        ]);

        try {
            DB::beginTransaction();

            $set = TariffSet::where('village_id', $village->id)
                ->where('status', 'published')
                ->orderByDesc('jalali_year')
                ->firstOrFail();

            // ساخت فیش
            $invoice = new TariffInvoice();
            $invoice->tracking_code = TariffInvoice::generateTrackingCode();
            $invoice->public_token = TariffInvoice::generatePublicToken();
            $invoice->village_id = $village->id;
            $invoice->tariff_set_id = $set->id;
            $invoice->payer_name = $data['payer_name'];
            $invoice->payer_national_id = Number::en($data['payer_national_id'] ?? '');
            $invoice->payer_mobile = Number::en($data['payer_mobile']);
            $invoice->subject = $data['subject'] ?? null;
            $invoice->issued_at = now();
            $invoice->due_date = now()->addDays(30); // مهلت پیش‌فرض 30 روز
            $invoice->status = 'issued';
            $invoice->save();

            // ساخت ردیف‌ها
            $sort = 1;
            foreach ($data['items'] as $lineData) {
                $item = TariffItem::with('title')->findOrFail($lineData['item_id']);

                // امنیت: فقط آیتم‌های همین تعرفه
                if ($item->tariff_set_id !== $set->id) {
                    throw new \RuntimeException('آیتم انتخاب‌شده متعلق به این تعرفه نیست.');
                }

                $line = TariffInvoiceLine::fromTariffItem(
                    item: $item,
                    amount: (int) $lineData['amount'],
                    discountPercent: (float) ($lineData['discount'] ?? 0),
                    inputs: $lineData['inputs'] ?? [],
                    notes: $lineData['notes'] ?? null
                );
                $line->invoice_id = $invoice->id;
                $line->sort_order = $sort++;
                $line->save();
            }

            // محاسبه جمع
            $invoice->recalculateTotal();

            DB::commit();

            // ریدایرکت به صفحه نمایش
            return redirect()->route('tariff.invoice.show', $invoice->public_token);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Invoice create failed', [
                'error' => $e->getMessage(),
                'village' => $slug,
            ]);
            return back()
                ->withInput()
                ->withErrors(['error' => 'خطا در صدور فیش: ' . $e->getMessage()]);
        }
    }

    /**
     * فرم پیگیری
     * GET /village/{slug}/tariff/invoice/track
     */
    public function trackForm(string $slug)
    {
        $village = Village::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('public.tariff.invoice.track', compact('village'));
    }

    /**
     * پیگیری
     * POST /village/{slug}/tariff/invoice/track
     */
    public function track(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $data = $request->validate([
            'tracking_code' => ['required', 'string', 'max:20'],
            'mobile'        => ['required', 'string', 'max:20'],
        ]);

        $invoice = TariffInvoice::where('tracking_code', strtoupper(trim($data['tracking_code'])))
            ->where('village_id', $village->id)
            ->first();

        if (!$invoice) {
            return back()->withErrors(['tracking_code' => 'فیشی با این کد پیگیری یافت نشد.'])->withInput();
        }

        // چک موبایل
        $inputMobile = Number::en($data['mobile']);
        $invoiceMobile = Number::en($invoice->payer_mobile);

        if ($invoiceMobile !== $inputMobile) {
            return back()->withErrors(['mobile' => 'شماره موبایل با کد پیگیری مطابقت ندارد.'])->withInput();
        }

        return redirect()->route('tariff.invoice.show', $invoice->public_token);
    }

    /**
     * نمایش فیش
     * GET /tariff/invoice/{token}
     */
    public function show(string $token)
    {
        $invoice = TariffInvoice::with(['lines', 'receipts', 'village', 'set'])
            ->where('public_token', $token)
            ->firstOrFail();

        $account = BankAccount::getDefaultForVillage($invoice->village_id);

        return view('public.tariff.invoice.show', compact('invoice', 'account'));
    }

    /**
     * آپلود رسید
     * POST /tariff/invoice/{token}/receipt
     */
    public function uploadReceipt(Request $request, string $token)
    {
        $invoice = TariffInvoice::where('public_token', $token)->firstOrFail();

        if (!$invoice->canUploadReceipt()) {
            return back()->withErrors(['error' => 'در وضعیت فعلی امکان آپلود رسید وجود ندارد.']);
        }

        $data = $request->validate([
            'receipt_file'   => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
            'paid_amount'    => ['required', 'integer', 'min:1'],
            'paid_at'        => ['required', 'string', 'max:20'],
            'bank_ref_no'    => ['nullable', 'string', 'max:100'],
            'installment_no' => ['nullable', 'integer', 'min:0', 'max:36'],
        ]);

        try {
            DB::beginTransaction();

            // ذخیره فایل
            $file = $request->file('receipt_file');
            $path = $file->store("tariff-receipts/{$invoice->id}", 'public');

            // ساخت رسید
            $receipt = new \App\Models\TariffReceipt();
            $receipt->invoice_id = $invoice->id;
            $receipt->receipt_file = $path;
            $receipt->receipt_mime = $file->getMimeType();
            $receipt->receipt_size = $file->getSize();
            $receipt->paid_amount = (int) Number::en($data['paid_amount']);
            $receipt->paid_at = now(); // TODO: تبدیل تاریخ شمسی
            $receipt->paid_at_jalali = Number::en($data['paid_at']);
            $receipt->bank_ref_no = $data['bank_ref_no'] ?? null;
            $receipt->installment_no = $data['installment_no'] ?? null;
            $receipt->status = 'pending';
            $receipt->ip_address = $request->ip();
            $receipt->user_agent = substr($request->userAgent() ?? '', 0, 500);
            $receipt->save();

            // آپدیت وضعیت فیش
            $invoice->status = 'receipt_uploaded';
            $invoice->save();

            DB::commit();

            return redirect()
                ->route('tariff.invoice.show', $invoice->public_token)
                ->with('success', 'رسید شما با موفقیت دریافت شد و در انتظار بررسی کارشناس است.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Receipt upload failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'خطا در آپلود رسید: ' . $e->getMessage()]);
        }
    }
}
