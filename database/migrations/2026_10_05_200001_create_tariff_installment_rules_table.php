<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tariff_installment_rules')) return;

        Schema::create('tariff_installment_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_set_id')->nullable()->constrained('tariff_sets')->cascadeOnDelete();
            $table->foreignId('tariff_title_id')->nullable()->constrained('tariff_titles')->cascadeOnDelete();

            // شرط: 1/3 نقد → 6 قسط بدون کارمزد
            // یا: 25٪ پیش‌پرداخت → تا 36 قسط با 2٪ ماهانه (سقف 24٪)
            $table->string('kind', 30); // one_third_cash | quarter_down_payment
            $table->decimal('down_payment_percent', 5, 2)->default(0);
            $table->integer('max_installments')->default(6);
            $table->decimal('monthly_interest_percent', 5, 2)->default(0);
            $table->decimal('max_total_interest_percent', 5, 2)->default(0);
            $table->boolean('is_penalty_eligible')->default(false); // جریمه ماده 99 قابل تقسیط نیست
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // درج پیش‌فرض (اگر جدول خالی بود)
        \DB::table('tariff_installment_rules')->insert([
            [
                'tariff_set_id' => null,
                'tariff_title_id' => null,
                'kind' => 'one_third_cash',
                'down_payment_percent' => 33.33,
                'max_installments' => 6,
                'monthly_interest_percent' => 0,
                'max_total_interest_percent' => 0,
                'is_penalty_eligible' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tariff_set_id' => null,
                'tariff_title_id' => null,
                'kind' => 'quarter_down_payment',
                'down_payment_percent' => 25,
                'max_installments' => 36,
                'monthly_interest_percent' => 2,
                'max_total_interest_percent' => 24,
                'is_penalty_eligible' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_installment_rules');
    }
};
