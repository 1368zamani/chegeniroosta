<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sections', function (Blueprint $table) {
            $table->text('facilities')->nullable()->after('description');
            $table->text('historical_sites')->nullable()->after('facilities');
            $table->integer('population_count')->nullable()->after('historical_sites');
        });
    }
    public function down(): void {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['facilities', 'historical_sites', 'population_count']);
        });
    }
};
