<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('tariff_invoices')->cascadeOnDelete();

            // ═══ فایل رسید ═══
            $table->string('receipt_file', 500);              // مسیر فایل
            $table->string('receipt_mime', 100)->nullable();  // نوع فایل
            $table->integer('receipt_size')->nullable();      // حجم (بایت)

            // ═══ اطلاعات پرداخت ═══
            $table->bigInteger('paid_amount')->default(0);
            $table->date('paid_at')->nullable();
            $table->string('paid_at_jalali', 20)->nullable(); // تاریخ شمسی نمایشی

            // ═══ اطلاعات بانکی ═══
            $table->string('bank_ref_no', 100)->nullable();   // شماره پیگیری بانک

            // ═══ تقسیط ═══
            $table->integer('installment_no')->nullable();    // 0 = پیش‌پرداخت

            // ═══ وضعیت بررسی ═══
            $table->enum('status', [
                'pending',    // در انتظار بررسی
                'approved',   // تایید شده
                'rejected',   // رد شده
            ])->default('pending');

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reject_reason')->nullable();

            // ═══ اطلاعات فنی ═══
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();

            $table->index(['invoice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_receipts');
    }
};
