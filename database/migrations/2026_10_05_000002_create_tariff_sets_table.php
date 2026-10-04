<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tariff_sets')) return;

        Schema::create('tariff_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->integer('jalali_year');

            // نرخ تورم اعلامی سال (درصد)
            $table->decimal('inflation_rate', 5, 2)->default(0);

            $table->enum('status', [
                'draft',
                'proposed',
                'village_council_approved',
                'district_council_approved',
                'finalized',
                'published',
            ])->default('draft');

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('announced_at')->nullable();
            $table->timestamp('locked_at')->nullable();

            // PDF مصوبه شورا
            $table->string('approval_document_path', 500)->nullable();
            $table->text('approval_notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // TODO(verify): مهلت‌های قانونی
            $table->date('deadline_approval')->nullable();  // ۱۵ بهمن
            $table->date('deadline_announcement')->nullable(); // پایان بهمن

            $table->timestamps();

            $table->unique(['village_id', 'jalali_year']);
            $table->index(['village_id', 'status']);
            $table->index('jalali_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_sets');
    }
};
