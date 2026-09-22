<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            if (!Schema::hasColumn('villages', 'county')) {
                $table->string('county')->nullable();
            }
            if (!Schema::hasColumn('villages', 'section')) {
                $table->string('section')->nullable();
            }
            // فیلدهای جدید
            $cols = [
                'welcome_text'         => 'string',
                'history_text'         => 'text',
                'naming_story'         => 'text',
                'distance_to_center'   => 'string',
                'hectares'             => 'decimal',
                'latitude'             => 'decimal',
                'longitude'            => 'decimal',
                'has_school'           => 'boolean',
                'has_health_house'     => 'boolean',
                'has_mosque'           => 'boolean',
                'has_water'            => 'boolean',
                'has_gas'              => 'boolean',
                'has_electricity'      => 'boolean',
                'has_internet'         => 'boolean',
                'has_sport_field'      => 'boolean',
                'council_phone'        => 'string',
                'council_members_count'=> 'integer',
                'dehyar_hours'         => 'string',
                'bank_account'         => 'string',
                'bank_name'            => 'string',
                'payment_id'           => 'string',
                'dehyar_photo'         => 'string',
            ];
            foreach ($cols as $name => $type) {
                if (!Schema::hasColumn('villages', $name)) {
                    if ($type === 'text') {
                        $table->text($name)->nullable();
                    } elseif ($type === 'boolean') {
                        $table->boolean($name)->default(false);
                    } elseif ($type === 'integer') {
                        $table->integer($name)->nullable();
                    } elseif ($type === 'decimal') {
                        $table->decimal($name, 12, 7)->nullable();
                    } else {
                        $table->string($name)->nullable();
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // برای امنیت، حذف نمی‌کنیم
    }
};
