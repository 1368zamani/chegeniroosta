<?php

namespace App\Http\Controllers;

/**
 * جایگزین مسیرهای قدیمی ماژول عوارض.
 *
 * پس از استقرار زیرسامانه جدید، تمام نشانیهای قدیمی تعرفه/عوارض به این صفحه
 * هدایت میشوند تا از خطای ۵۰۰ ناشی از حذف جداول قدیمی جلوگیری شود و کاربر
 * به سامانه جدید هدایت گردد. سایر ماژولها (works-fees، sitemap و …) دستنخوردهاند.
 */
class LegacyLevyController extends Controller
{
    public function index()
    {
        return view('admin.levy.legacy');
    }
}
