<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('tracking_code')->unique();  // کد پیگیری
            $table->string('full_name');
            $table->string('phone');
            $table->string('subject');                 // آب، برق، گاز، ...
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->string('status')->default('submitted'); // submitted/reviewing/assigned/processing/resolved/rejected
            $table->text('admin_response')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_requests');
    }
};
