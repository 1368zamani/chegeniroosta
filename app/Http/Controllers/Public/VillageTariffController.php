<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\TariffInvoice;
use App\Models\TariffInvoiceLine;
use App\Models\TariffItem;
use App\Models\TariffSet;
use App\Models\Village;
use App\Support\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VillageTariffController extends Controller
{
    /**
     * صفحه نمایش تعرفه‌های روستا با قابلیت انتخاب
     * GET /village/{slug}/tariff
     */
    public function index(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $set = TariffSet::where('village_id', $village->id)
            ->where('status', 'published')
            ->orderByDesc('jalali_year')
            ->first();

        if (!$set) {
            return view('village.tariff.no-tariff', compact('village'));
        }

        $items = TariffItem::with('title')
            ->where('tariff_set_id', $set->id)
            ->where('is_active', true)
            ->whereHas('title')
            ->orderBy('id')
            ->get()
            ->groupBy(fn($i) => $i->title->type ?? 'levy');

        $account = BankAccount::getDefaultForVillage($village->id);

        return view('village.tariff.index', compact('village', 'set', 'items', 'account'));
    }

    /**
     * صدور فیش جدید از انتخاب کاربر
     * POST /village/{slug}/tariff/invoice
     */
    public function issueInvoice(Request $request, string $slug)
    {
        Log::info('[STEP-1] Start issueInvoice', ['slug' => $slug, 'data' => $request->all()]);

        $village = Village::where('slug', $slug)->where('is_active', true)->firstOrFail();

        Log::info('[STEP-2] Village found', ['village_id' => $village->id]);

        $data = $request->validate([
            'payer_name'        => ['required', 'string', 'max:200'],
            'payer_national_id' => ['nullable', 'string', 'max:20'],
            'payer_mobile'      => ['required', 'string', 'max:20'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.id'        => ['required', 'integer', 'exists:tariff_items,id'],
            'items.*.amount'    => ['required', 'integer', 'min:0'],
            'items.*.inputs'    => ['nullable', 'array'],
        ]);

        Log::info('[STEP-3] Validation passed', ['data' => $data]);

        try {
            DB::beginTransaction();

            Log::info('[STEP-4] Transaction started');

            $set = TariffSet::where('village_id', $village->id)
                ->where('status', 'published')
                ->orderByDesc('jalali_year')
                ->firstOrFail();

            Log::info('[STEP-5] TariffSet found', ['set_id' => $set->id]);

            // ساخت فیش
            $invoice = new TariffInvoice();
            $invoice->tracking_code = TariffInvoice::generateTrackingCode();
            $invoice->public_token = TariffInvoice::generatePublicToken();
            $invoice->village_id = $village->id;
            $invoice->tariff_set_id = $set->id;
            $invoice->payer_name = $data['payer_name'];
            $invoice->payer_national_id = Number::en($data['payer_national_id'] ?? '');
            $invoice->payer_mobile = Number::en($data['payer_mobile']);
            $invoice->issued_at = now();
            $invoice->due_date = now()->addDays(30);
            $invoice->status = 'issued';
            Log::info('[STEP-6] About to save invoice');

            $invoice->save();

            Log::info('[STEP-7] Invoice saved', ['invoice_id' => $invoice->id]);

            // ساخت ردیف‌ها
            $sort = 1;
            foreach ($data['items'] as $lineData) {
                $item = TariffItem::with('title')->findOrFail($lineData['id']);

                // امنیت: آیتم باید در همین set باشه
                if ($item->tariff_set_id !== $set->id) {
                    throw new \RuntimeException('آیتم انتخاب‌شده متعلق به این تعرفه نیست.');
                }

                $line = new TariffInvoiceLine();
                $line->invoice_id = $invoice->id;
                $line->tariff_item_id = $item->id;
                $line->title_name = optional($item->title)->name_fa ?? 'نامشخص';
                $line->amount_before = (int) $lineData['amount'];
                $line->discount_percent = 0;
                $line->discount_amount = 0;
                $line->amount = (int) $lineData['amount'];
                $line->inputs = $lineData['inputs'] ?? [];
                $line->sort_order = $sort++;
                $line->save();
            }

            $invoice->recalculateTotal();

            DB::commit();

            return redirect()
                ->route('village.tariff.invoice.show', $invoice->public_token)
                ->with('success', 'فیش با موفقیت صادر شد. کد پیگیری: ' . $invoice->tracking_code);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Invoice issue failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'خطا: ' . $e->getMessage()]);
        }
    }

    /**
     * نمایش فیش (صفحه عمومی)
     */
    public function showInvoice(string $token)
    {
        $invoice = TariffInvoice::with(['lines', 'receipts', 'village', 'set'])
            ->where('public_token', $token)
            ->firstOrFail();

        // ═══ پاس دادن village برای layout ═══
        $village = $invoice->village;
        $account = BankAccount::getDefaultForVillage($invoice->village_id);

        return view('village.tariff.invoice-show', compact('invoice', 'village', 'account'));
    }

    /**
     * آپلود رسید پرداخت
     */
    public function uploadReceipt(Request $request, string $token)
    {
        $invoice = TariffInvoice::where('public_token', $token)->firstOrFail();

        if (!in_array($invoice->status, ['issued', 'pending_review'])) {
            return back()->withErrors(['error' => 'در وضعیت فعلی امکان آپلود رسید وجود ندارد.']);
        }

        $data = $request->validate([
            'receipt_file' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
            'paid_amount'  => ['required', 'integer', 'min:1'],
            'paid_at'      => ['required', 'string', 'max:20'],
            'bank_ref_no'  => ['nullable', 'string', 'max:100'],
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('receipt_file');
            $path = $file->store("tariff-receipts/{$invoice->id}", 'public');

            \App\Models\TariffReceipt::create([
                'invoice_id'       => $invoice->id,
                'receipt_file'     => $path,
                'receipt_mime'     => $file->getMimeType(),
                'receipt_size'     => $file->getSize(),
                'paid_amount'      => (int) Number::en($data['paid_amount']),
                'paid_at'          => now(),
                'paid_at_jalali'   => Number::en($data['paid_at']),
                'bank_ref_no'      => $data['bank_ref_no'] ?? null,
                'status'           => 'pending',
                'ip_address'       => $request->ip(),
                'user_agent'       => substr($request->userAgent() ?? '', 0, 500),
            ]);

            $invoice->status = 'receipt_uploaded';
            $invoice->save();

            DB::commit();

            return redirect()
                ->route('village.tariff.invoice.show', $invoice->public_token)
                ->with('success', 'رسید شما با موفقیت دریافت شد و در انتظار تایید کارشناس مالی است.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا: ' . $e->getMessage()]);
        }
    }

    /**
     * پیگیری فیش
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

        if (Number::en($invoice->payer_mobile) !== Number::en($data['mobile'])) {
            return back()->withErrors(['mobile' => 'شماره موبایل مطابقت ندارد.'])->withInput();
        }

        return redirect()->route('village.tariff.invoice.show', $invoice->public_token);
    }
}
