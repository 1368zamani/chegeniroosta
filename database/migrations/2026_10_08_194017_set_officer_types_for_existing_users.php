<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // تعیین officer_type برای کارشناسان شناخته‌شده
        $map = [
            'technical@example.com' => 'technical',
            'financial@example.com' => 'financial',
        ];

        foreach ($map as $email => $type) {
            DB::table('users')
                ->where('email', $email)
                ->whereNull('officer_type')
                ->update(['officer_type' => $type]);
        }
    }

    public function down(): void
    {
        DB::table('users')
            ->whereIn('email', ['technical@example.com', 'financial@example.com'])
            ->update(['officer_type' => null]);
    }
};
