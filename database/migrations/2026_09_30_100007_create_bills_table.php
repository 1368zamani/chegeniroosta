<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('bills')) {
            Schema::create('bills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dehyari_id')->constrained('dehyaris')->onDelete('cascade');
                $table->foreignId('fee_tariff_id')->nullable()->constrained('fee_tariffs')->onDelete('set null');

                $table->string('bill_number', 30)->unique();       // شناسه قبض
                $table->string('payment_id', 30)->unique();        // شناسه پرداخت

                // اطلاعات مؤدی
                $table->string('taxpayer_name', 255);
                $table->string('national_code', 20)->nullable();
                $table->string('mobile', 20)->nullable();
                $table->string('address', 500)->nullable();

                // Snapshot از تعرفه (تا تغییر تعرفه بعداً روی قبض اثر نگذارد)
                $table->string('fee_category_name', 255);
                $table->string('fee_type_name', 255);
                $table->integer('year');
                $table->string('unit', 30);
                $table->string('formula_type', 50);
                $table->text('formula_display')->nullable();        // فرمول قابل نمایش
                $table->json('parameters_snapshot');                // پارامترهای ورودی کاربر
                $table->json('calculation_steps');                  // مراحل محاسبه برای شفافیت

                // مبالغ
                $table->bigInteger('amount_rial');                  // مبلغ نهایی به ریال
                $table->bigInteger('amount_paid')->default(0);      // مبلغ پرداخت‌شده

                // تاریخ‌ها
                $table->date('issue_date');
                $table->date('due_date')->nullable();

                // وضعیت: unpaid, pending_review, paid, rejected, cancelled
                $table->string('status', 30)->default('unpaid');

                // شماره حساب نمایش داده شده به کاربر (snapshot)
                $table->string('payment_bank', 100)->nullable();
                $table->string('payment_account', 50)->nullable();
                $table->string('payment_card', 30)->nullable();
                $table->string('payment_sheba', 30)->nullable();

                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

                $table->timestamps();

                $table->index(['dehyari_id', 'status']);
                $table->index(['year', 'status']);
                $table->index('national_code');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
