<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ضرایب مرجع هر روستا (M, O, P) — برگرفته از اکسل، قابل ویرایش توسط کارشناس
        Schema::create('levy_village_params', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->unique()->constrained('villages')->cascadeOnDelete();
            $table->string('group', 4)->default('الف');
            $table->unsignedBigInteger('transaction_value')->default(39000); // M
            $table->unsignedInteger('construction_coefficient')->default(30); // O
            $table->unsignedInteger('density_coefficient')->default(30);      // P
            $table->char('density_area_source', 1)->default('Q'); // Q=مساحت ساخت، R=مساحت ملک (استثناء اکسل)
            $table->string('source_note')->nullable(); // مشخص میکند عدد از اکسل آمده یا پیشفرض گروه
            $table->timestamps();
        });

        // شماره حسابهای دهیاریها (شیت دوم اکسل)
        Schema::create('levy_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_no')->nullable();
            $table->string('label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // پرونده مستقل عوارض هر روستا (روستاهای موجود سامانه)
        Schema::create('levy_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('jalali_year', 4);
            $table->string('applicant_name')->nullable();
            $table->string('dehyari_name')->nullable();
            $table->string('dehyari_account_no')->nullable();
            $table->string('iban')->nullable();
            $table->string('id_number')->nullable();
            $table->string('village_group')->nullable();
            $table->string('class_status')->nullable();
            $table->decimal('construction_area', 15, 2)->nullable(); // Q — ورودی کارشناس
            $table->decimal('property_area', 15, 2)->nullable();      // R — ورودی کارشناس
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft|incomplete|complete
            $table->unsignedTinyInteger('completion_percent')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['village_id', 'jalali_year']);
        });

        // سه ردیف عوارض سمت چپ اکسل (F4:F6 / G4:G6)
        Schema::create('levy_file_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('levy_file_id')->constrained('levy_files')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // سوابق تغییرات
        Schema::create('levy_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('levy_file_id')->constrained('levy_files')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->json('changes')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levy_audit_logs');
        Schema::dropIfExists('levy_file_lines');
        Schema::dropIfExists('levy_files');
        Schema::dropIfExists('levy_accounts');
        Schema::dropIfExists('levy_village_params');
    }
};
