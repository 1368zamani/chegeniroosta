<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('tariff_invoices')->cascadeOnDelete();

            // اتصال اختیاری به آیتم تعرفه
            $table->foreignId('tariff_item_id')->nullable()->constrained('tariff_items')->nullOnDelete();

            // ═══ اطلاعات ردیف ═══
            $table->string('title_name', 500);                // عنوان
            $table->bigInteger('amount_before')->default(0);  // مبلغ قبل از تخفیف
            $table->decimal('discount_percent', 5, 2)->default(0);  // درصد تخفیف
            $table->bigInteger('discount_amount')->default(0);      // مبلغ تخفیف
            $table->bigInteger('amount')->default(0);          // مبلغ نهایی

            // ═══ توضیحات ═══
            $table->text('notes')->nullable();
            $table->json('inputs')->nullable();                // ورودی‌های محاسبه

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['invoice_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_invoice_lines');
    }
};
