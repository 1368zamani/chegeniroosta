<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // village_projects — چک و اضافه کردن
        if (Schema::hasTable('village_projects')) {
            Schema::table('village_projects', function (Blueprint $table) {
                $cols = ['title', 'description', 'status', 'progress', 'contractor', 'budget', 'start_date', 'end_date', 'before_image', 'after_image', 'cover_image', 'sort_order'];
                foreach ($cols as $c) {
                    if (!Schema::hasColumn('village_projects', $c)) {
                        if ($c === 'description') {
                            $table->text($c)->nullable();
                        } elseif ($c === 'progress' || $c === 'sort_order') {
                            $table->integer($c)->default(0);
                        } elseif ($c === 'start_date' || $c === 'end_date') {
                            $table->date($c)->nullable();
                        } else {
                            $table->string($c)->nullable();
                        }
                    }
                }
            });
        }
    }

    public function down(): void {}
};
