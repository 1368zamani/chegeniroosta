<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * درخواستهای مردمی عوارض (احداث بنا / پسماند)
 *
 * جایگزین ساختار قدیمی tariff_invoices است که پس از بکاپ کامل حذف شد.
 * این جدولها فقط اطلاعات ثبتنام، مبلغ محاسبهشده و وضعیت پرداخت را نگه میدارند؛
 * هیچ ضریب یا قانون محاسبهای در آنها ذخیره نمیشود.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levy_public_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 32)->unique();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('type', 20); // construction|waste
            $table->string('payer_name', 200);
            $table->string('payer_mobile', 20);
            $table->string('national_id', 20)->nullable();
            $table->text('notes')->nullable();

            // ورودیها و خروجیهای محاسبهٔ اکسل (برای درخواست احداث بنا)
            $table->decimal('construction_area', 15, 2)->nullable(); // Q
            $table->decimal('property_area', 15, 2)->nullable();      // R
            $table->bigInteger('transaction_value')->default(0);      // M
            $table->bigInteger('final_coefficient')->default(0);      // N
            $table->bigInteger('residential_levy')->default(0);       // S
            $table->bigInteger('density_levy')->default(0);           // T
            $table->bigInteger('final_levy')->default(0);             // U

            $table->bigInteger('amount');                              // مبلغ قابل پرداخت
            $table->string('account_no', 100)->nullable();             // شماره حساب اعلامشده به کاربر
            $table->string('iban', 50)->nullable();
            $table->string('account_holder', 200)->nullable();
            $table->string('status', 20)->default('awaiting_receipt'); // awaiting_receipt|under_review|approved|rejected
            $table->string('receipt_path')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['village_id', 'created_at']);
        });

        Schema::create('levy_public_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('levy_public_request_id')->constrained('levy_public_requests')->cascadeOnDelete();
            $table->foreignId('tariff_title_id')->nullable()->constrained('tariff_titles')->nullOnDelete();
            $table->string('title_snapshot', 255);
            $table->string('unit', 50)->nullable();
            $table->bigInteger('unit_price')->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->bigInteger('amount');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('levy_public_request_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('levy_public_request_id')->constrained('levy_public_requests')->cascadeOnDelete();
            $table->string('action', 60);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levy_public_request_events');
        Schema::dropIfExists('levy_public_request_items');
        Schema::dropIfExists('levy_public_requests');
    }
};
