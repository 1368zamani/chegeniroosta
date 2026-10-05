<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tariff_exemptions')) return;

        Schema::create('tariff_exemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_set_id')->constrained('tariff_sets')->cascadeOnDelete();
            $table->foreignId('tariff_title_id')->nullable()->constrained('tariff_titles')->nullOnDelete();

            // disabled | elderly | martyr_family | clergy | educational | charity | custom
            $table->string('kind', 30);
            // درصد تخفیف؛ 100 = معافیت کامل
            $table->decimal('discount_percent', 5, 2)->default(100);
            $table->string('description', 500);

            // اگر null باشد یعنی بدون محدودیت زمانی
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            $table->text('statutory_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['tariff_set_id', 'is_active']);
            $table->index(['tariff_title_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_exemptions');
    }
};
