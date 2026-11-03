<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * شمارنده‌ی بازدید و دانلود برای همه‌ی انواع محتوا.
 *
 * طراحی به‌صورت «چندریختی» (polymorphic) است تا با یک جدول،
 * آمار اخبار بخش، اخبار روستا، مصوبات، روستاها و ... ذخیره شود.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('content_stats')) {
            return;
        }

        Schema::create('content_stats', function (Blueprint $table) {
            $table->id();

            // نوع و شناسه‌ی محتوا (مثلاً App\Models\SectionNews + 12)
            $table->string('statable_type', 191);
            $table->unsignedBigInteger('statable_id');

            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('downloads')->default(0);

            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();

            $table->timestamps();

            // برای هر محتوا فقط یک ردیف آمار
            $table->unique(['statable_type', 'statable_id'], 'content_stats_unique');
            $table->index('views');
            $table->index('downloads');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_stats');
    }
};
