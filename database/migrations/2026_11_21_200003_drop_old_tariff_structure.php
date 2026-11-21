<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * حذف فیزیکی ساختار قدیمی ماژول عوارض
 * (پس از تهیه بکاپ کامل: /root/backups/levy-redesign-20261121-194658)
 *
 * نکته: دو جدول tariff_titles و tariff_change_logs نگهداری میشوند، زیرا ماژول
 * مستقل «عوارض پسماند و ساختوساز» (works-fees) از آنها استفاده میکند و حذف آنها
 * به سایر قسمتهای سامانه آسیب میرساند. سایر جداول قدیمی عوارض حذف میشوند.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ترتیب: جداول فرزند قبل از والد
        $tables = [
            'tariff_invoice_lines',
            'tariff_receipts',
            'tariff_workflow_logs',
            'tariff_exemptions',
            'tariff_installment_rules',
            'tariff_items',
            'tariff_parameters',
            'tariff_invoices',
            'tariff_sets',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        // بازیابی کامل از فایل بکاپ:
        // mysql -u... chegeniroosta < /root/backups/levy-redesign-20261121-194658/tariff_tables_only.sql
    }
};
