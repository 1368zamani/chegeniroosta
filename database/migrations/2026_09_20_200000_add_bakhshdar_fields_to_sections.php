<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->string('bakhshdar_name')->nullable()->after('description');
            $table->string('bakhshdar_photo')->nullable()->after('bakhshdar_name');
            $table->text('bakhshdar_bio')->nullable()->after('bakhshdar_photo');
            $table->string('bakhshdar_phone')->nullable()->after('bakhshdar_bio');
            $table->string('bakhshdar_email')->nullable()->after('bakhshdar_phone');
            $table->text('bakhshdar_education')->nullable()->after('bakhshdar_email');
            $table->text('bakhshdar_experience')->nullable()->after('bakhshdar_education');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn([
                'bakhshdar_name',
                'bakhshdar_photo',
                'bakhshdar_bio',
                'bakhshdar_phone',
                'bakhshdar_email',
                'bakhshdar_education',
                'bakhshdar_experience',
            ]);
        });
    }
};
