<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * ثبت فیش واریزی دستی توسط کاربر (بدون درگاه).
 * فلوی کار:
 *   1) کاربر فیش را آپلود می‌کند
 *   2) payment با method=manual و status=pending_review ذخیره می‌شود
 *   3) bill به pending_review تغییر وضعیت می‌دهد
 *   4) ادمین بعداً تأیید یا رد می‌کند
 */
class FeePaymentController extends Controller
{
    public function upload(Request $request, string $billNumber)
    {
        $bill = Bill::where('bill_number', $billNumber)->firstOrFail();

        if ($bill->status === 'paid') {
            return back()->withErrors(['bill' => 'این قبض قبلاً پرداخت شده است.']);
        }

        if ($bill->status === 'pending_review') {
            return back()->withErrors(['bill' => 'فیش شما در حال بررسی است. لطفاً منتظر تأیید بمانید.']);
        }

        $data = $request->validate([
            'bank_reference' => 'required|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'paid_at'        => 'required|string|max:20',    // تاریخ شمسی مثل 1405/07/08
            'amount_paid'    => 'required|integer|min:1000',
            'receipt'        => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            'note'           => 'nullable|string|max:500',
        ], [
            'bank_reference.required' => 'شماره پیگیری بانکی الزامی است.',
            'paid_at.required'        => 'تاریخ واریز الزامی است.',
            'amount_paid.required'    => 'مبلغ واریزی الزامی است.',
            'amount_paid.min'         => 'مبلغ واریزی معتبر نیست.',
            'receipt.required'        => 'عکس فیش الزامی است.',
            'receipt.mimes'           => 'فرمت فیش باید JPG، PNG، WEBP یا PDF باشد.',
            'receipt.max'             => 'حجم فیش نباید بیشتر از ۲ مگابایت باشد.',
        ]);

        // اطلاعات فایل را قبل از closure استخراج می‌کنیم
        $file           = $request->file('receipt');
        $originalName   = $file->getClientOriginalName();
        $mimeType       = $file->getClientMimeType();
        $fileSize       = $file->getSize();

        try {
            $receiptPath = $this->storeReceipt($file);
        } catch (\Throwable $e) {
            return back()->withErrors(['receipt' => 'خطا در ذخیره فایل: ' . $e->getMessage()]);
        }

        DB::transaction(function () use ($bill, $data, $receiptPath, $originalName, $mimeType, $fileSize) {
            $payment = Payment::create([
                'bill_id'         => $bill->id,
                'method'          => 'manual',
                'payer_name'      => $bill->taxpayer_name,
                'bank_reference'  => $data['bank_reference'],
                'note'            => $data['note'] ?? null,
                'amount_rial'     => $data['amount_paid'],
                'paid_at'         => $this->normalizeDate($data['paid_at']),
                'status'          => 'pending_review',
            ]);

            PaymentReceipt::create([
                'payment_id'    => $payment->id,
                'file_path'     => $receiptPath,
                'original_name' => $originalName,
                'mime_type'     => $mimeType,
                'size'          => $fileSize,
            ]);

            $bill->update(['status' => 'pending_review']);
        });

        return redirect()
            ->route('fees.bill.show', $bill->bill_number)
            ->with('success', '✅ فیش شما ثبت شد و در انتظار تأیید دهیاری است.');
    }

    /** صفحه پیگیری قبض با کد و موبایل */
    public function tracking(Request $request)
    {
        $bill = null;
        $error = null;

        if ($request->filled('tracking_code')) {
            $bill = Bill::where('bill_number', $request->tracking_code)->first();

            if (!$bill) {
                $error = 'قبضی با این کد پیگیری یافت نشد.';
            } elseif ($request->filled('mobile') && $bill->mobile !== $request->mobile) {
                $error = 'شماره موبایل با این قبض مطابقت ندارد.';
                $bill = null;
            }
        }

        return view('fees.tracking', compact('bill', 'error'));
    }

    private function storeReceipt($file): string
    {
        $dir = 'receipts/' . now()->format('Y/m');
        $name = 'rcpt_' . now()->format('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($dir, $name, 'local'); // storage/app/private/...
    }

    /** تبدیل تاریخ شمسی به میلادی برای ذخیره در DB */
    private function normalizeDate(string $input): string
    {
        // اگر کاربر میلادی فرستاد، همان را برگردان
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
            return $input;
        }
        // اگر شمسی بود (1405/07/08) تبدیل کن
        if (preg_match('#^(\d{4})/(\d{2})/(\d{2})$#', $input, $m)) {
            try {
                return \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $input)
                    ->toCarbon()->toDateString();
            } catch (\Throwable $e) {
                return now()->toDateString();
            }
        }
        return now()->toDateString();
    }
}
