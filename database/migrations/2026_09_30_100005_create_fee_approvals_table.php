<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('fee_approvals')) {
            Schema::create('fee_approvals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fee_tariff_id')->constrained('fee_tariffs')->onDelete('cascade');
                $table->string('approval_number', 100)->nullable();  // شماره مصوبه
                $table->date('approval_date')->nullable();
                $table->string('authority', 255)->nullable();        // مرجع تصویب: شورای اسلامی روستا
                $table->date('announcement_date')->nullable();       // تاریخ ابلاغ
                $table->text('notes')->nullable();
                $table->string('document_path')->nullable();          // فایل PDF مصوبه
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_approvals');
    }
};
