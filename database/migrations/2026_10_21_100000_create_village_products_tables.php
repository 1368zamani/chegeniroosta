<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ماژول «محصولات روستا» — برای فروش محصولات محلی.
 *
 * سه جدول:
 *   village_products        : خود محصول (مشخصات، قیمت، فروشنده)
 *   village_product_images  : تصاویر محصول (چندگانه)
 *   village_product_files   : فایل‌های ضمیمه (کاتالوگ، مشخصات، مجوز) + شمارنده دانلود
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('village_products')) {
            Schema::create('village_products', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('village_id');
                $table->unsignedBigInteger('user_id')->nullable();

                $table->string('title', 200);
                $table->string('slug', 220);
                $table->string('category', 80)->nullable();

                $table->text('description')->nullable();
                $table->string('short_description', 300)->nullable();

                // قیمت (تومان) — خالی = «تماس بگیرید»
                $table->decimal('price', 15, 0)->nullable();
                $table->string('unit', 40)->nullable();           // کیلوگرم، عدد، بسته…
                $table->string('quantity', 60)->nullable();       // موجودی: «۱۰۰ کیلو»

                // فروشنده / تماس
                $table->string('seller_name', 120)->nullable();
                $table->string('seller_phone', 30)->nullable();

                // محل تحویل
                $table->string('delivery_info', 300)->nullable();

                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_available')->default(true);

                $table->unsignedInteger('sort_order')->default(0);
                $table->unsignedInteger('views')->default(0);

                $table->timestamps();

                $table->index(['village_id', 'is_active', 'sort_order'], 'vp_village_idx');
                $table->index(['village_id', 'slug'], 'vp_slug_idx');
            });
        }

        if (! Schema::hasTable('village_product_images')) {
            Schema::create('village_product_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('image', 500);
                $table->string('caption', 200)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index('product_id');
            });
        }

        if (! Schema::hasTable('village_product_files')) {
            Schema::create('village_product_files', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');

                $table->string('title', 200);
                $table->string('file_path', 500);
                $table->string('file_name', 255);
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('mime_type', 120)->nullable();

                $table->unsignedInteger('download_count')->default(0);
                $table->unsignedInteger('sort_order')->default(0);

                $table->timestamps();

                $table->index('product_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('village_product_files');
        Schema::dropIfExists('village_product_images');
        Schema::dropIfExists('village_products');
    }
};
