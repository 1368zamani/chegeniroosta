<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_invoices', function (Blueprint $table) {
            $table->id();

            // ═══ شناسه‌ها ═══
            $table->string('tracking_code', 20)->unique();          // کد پیگیری
            $table->string('public_token', 64)->unique();            // توکن عمومی برای لینک

            // ═══ اتصالات ═══
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->foreignId('tariff_set_id')->nullable()->constrained('tariff_sets')->nullOnDelete();

            // ═══ اطلاعات پرداخت‌کننده (خوداظهاری) ═══
            $table->string('payer_name', 200);
            $table->string('payer_national_id', 20)->nullable();     // کد ملی خوداظهاری
            $table->string('payer_mobile', 20)->nullable();

            // ═══ اطلاعات فیش ═══
            $table->string('subject', 500)->nullable();              // موضوع
            $table->bigInteger('total')->default(0);                 // جمع کل
            $table->bigInteger('discount_total')->default(0);         // جمع تخفیف

            // ═══ تقسیط (اگر باشد) ═══
            $table->json('installment_plan')->nullable();
            $table->boolean('is_installment')->default(false);

            // ═══ تاریخ‌ها ═══
            $table->timestamp('issued_at')->nullable();
            $table->date('due_date')->nullable();

            // ═══ وضعیت ═══
            $table->enum('status', [
                'pending_review',    // در انتظار بررسی (اگر معافیت/تقسیط درخواست شده)
                'issued',            // صادر شده (آماده پرداخت)
                'receipt_uploaded',  // رسید بارگذاری شده
                'paid',              // پرداخت‌شده و تایید شده
                'cancelled',         // ابطال‌شده
            ])->default('issued');

            $table->text('review_note')->nullable();                 // توضیح کارشناس

            // ═══ اطلاعات پرداخت ═══
            $table->timestamp('paid_at')->nullable();
            $table->bigInteger('paid_amount')->default(0);

            // ═══ اختیاری — اتصال به درخواست مردمی ═══
            $table->foreignId('village_request_id')->nullable()->constrained('village_requests')->nullOnDelete();

            $table->timestamps();

            // ═══ ایندکس‌ها ═══
            $table->index(['village_id', 'status']);
            $table->index(['payer_national_id']);
            $table->index(['payer_mobile']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_invoices');
    }
};
