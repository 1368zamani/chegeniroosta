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

        $account = BankAccount::getDefaultForVillage($village->id);

        /*
         * ═══ بازطراحی «عوارض» ═══
         *
         * نمایش بر اساس «عنوان عوارض» است، نه ردیف خام.
         *
         * چرا؟ جدول tariff_items برای بعضی عناوین ده‌ها ردیف «ضریب»
         * دارد (مثلاً عوارض زیربنا ۲۵ ردیف با اعداد ۲۰، ۲۵، ۳۰…).
         * نمایش آن‌ها به شهروند بی‌معنا و گیج‌کننده بود.
         *
         * حالا برای هر عنوان یک کارت ساخته می‌شود که «مبلغ پایه»
         * (قابل ویرایش توسط کارشناس) را نشان می‌دهد.
         */
        $titles = \App\Models\TariffTitle::categorized()
            ->active()
            ->ordered()
            ->get();

        // اگر هنوز دسته‌بندی اعمال نشده، به صفحه‌ی قبلی برگرد تا خالی نشود
        if ($titles->isEmpty()) {
            $legacy = TariffItem::with('title')
                ->where('tariff_set_id', $set->id)
                ->where('is_active', true)
                ->whereHas('title')
                ->orderBy('id')
                ->get()
                ->groupBy(fn ($i) => $i->title->type ?? 'levy');

            return view('village.tariff.legacy', [
                'village' => $village,
                'set' => $set,
                'items' => $legacy,
                'account' => $account,
            ]);
        }

        // ردیف‌های همین تعرفه، گروه‌بندی‌شده بر اساس عنوان
        $setItems = TariffItem::where('tariff_set_id', $set->id)
            ->where('is_active', true)
            ->get()
            ->groupBy('tariff_title_id');

        $groups = ['waste' => collect(), 'new_build' => collect(), 'renovation' => collect()];

        foreach ($titles as $title) {
            $items = $setItems->get($title->id, collect());

            // ردیف نماینده: بزرگ‌ترین مبلغ (یعنی ضریب واقعی، نه صفرِ پیش‌فرض)
            $rep = $items->sortByDesc('final_amount')->first();

            // مبلغ نمایشی = مبلغ پایه‌ی قابل ویرایش؛ اگر ثبت نشده بود، مبلغ ردیف
            $amount = (int) ($title->base_amount ?? ($rep->final_amount ?? 0));

            $card = (object) [
                'title'       => $title,
                'item'        => $rep,
                'item_count'  => $items->count(),
                'amount'      => $amount,
                'selectable'  => $rep !== null && $amount > 0,
            ];

            $key = $title->category === \App\Models\TariffTitle::CATEGORY_WASTE
                ? 'waste'
                : ($title->subcategory === \App\Models\TariffTitle::SUB_RENOVATION ? 'renovation' : 'new_build');

            $groups[$key]->push($card);
        }

        return view('village.tariff.index', [
            'village' => $village,
            'set'     => $set,
            'groups'  => $groups,
            'account' => $account,
        ]);
    }

    /**
     * صدور فیش جدید از انتخاب کاربر
     * POST /village/{slug}/tariff/invoice
     */
    public function issueInvoice(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $data = $request->validate([
            'payer_name'        => ['required', 'string', 'max:200'],
            'payer_national_id' => ['nullable', 'string', 'max:20'],
            'payer_mobile'      => ['required', 'string', 'max:20'],
            'subject'           => ['nullable', 'string', 'max:500'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.id'        => ['required', 'integer', 'exists:tariff_items,id'],
            'items.*.amount'    => ['required', 'integer', 'min:0'],
            'items.*.discount'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.notes'     => ['nullable', 'string'],
            'items.*.inputs'    => ['nullable', 'array'],
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
            $invoice->due_date = now()->addDays(30);
            $invoice->status = 'issued';

            $invoice->save();

            // ساخت ردیف‌ها
            $sort = 1;
            foreach ($data['items'] as $lineData) {
                $item = TariffItem::with('title')->findOrFail($lineData['id']);

                // امنیت: آیتم باید در همین set باشه
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
