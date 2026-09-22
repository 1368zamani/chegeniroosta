<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('village_news', function (Blueprint $table) {
            if (!Schema::hasColumn('village_news', 'slug')) {
                $table->string('slug')->nullable()->after('title');
            }
            if (!Schema::hasColumn('village_news', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('body');
            }
            if (!Schema::hasColumn('village_news', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('image');
            }
            if (!Schema::hasColumn('village_news', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('is_pinned');
            }
            if (!Schema::hasColumn('village_news', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_published');
            }
            if (!Schema::hasColumn('village_news', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('village_id');
            }
        });
    }

    public function down(): void
    {
        // برای امنیت حذف نمی‌کنیم
    }
};
