@extends('admin.village.layout')
@section('title', 'راهنمای جامع پنل')

@section('content')
<style>
    .guide-container { max-width: 1000px; margin: auto; font-family: Tahoma, sans-serif; line-height: 2; color: #333; }
    .guide-header { text-align: center; padding: 30px 20px; background: linear-gradient(135deg, #5c2313, #8e2f18); color: white; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    .guide-header h1 { margin: 0; font-size: 26px; color: #f5b841; }
    .guide-header p { margin: 10px 0 0; font-size: 15px; opacity: 0.9; }
    
    .section-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-right: 5px solid #c94a2b; }
    .section-card h2 { color: #5c2313; font-size: 20px; margin-top: 0; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f5efe4; padding-bottom: 15px; }
    .section-card h2 span { background: #fdeee7; padding: 5px 10px; border-radius: 8px; font-size: 22px; }
    
    .menu-path { background: #fffdf9; border: 1px solid #e6d8c3; border-radius: 8px; padding: 10px 15px; margin: 10px 0; font-size: 14px; color: #5c2313; font-weight: bold; }
    .menu-path::before { content: '📌 '; }
    
    .step-list { list-style: none; padding: 0; margin: 15px 0; }
    .step-list li { position: relative; padding-right: 35px; margin-bottom: 15px; }
    .step-list li::before { content: '✓'; position: absolute; right: 0; top: 2px; width: 22px; height: 22px; background: #c94a2b; color: white; border-radius: 50%; text-align: center; font-size: 14px; line-height: 22px; font-weight: bold; }
    
    .alert-box { background: #fff7dc; border-right: 4px solid #ff9800; padding: 15px; border-radius: 8px; margin: 20px 0; font-size: 14px; }
    .alert-box strong { color: #a8730f; }
    
    .menu-list { background: #fff7e9; border: 1px solid #fbdcd0; border-radius: 8px; padding: 15px 20px; margin: 15px 0; }
    .menu-list h4 { color: #8e2f18; margin: 0 0 10px; font-size: 15px; }
    .menu-list ul { margin: 0; padding-right: 20px; }
    .menu-list li { color: #c94a2b; margin-bottom: 5px; font-weight: 600; }
</style>

<div class="guide-container">
    <div class="guide-header">
        <h1>📘 راهنمای جامع پنل مدیریت روستا</h1>
        <p>همه آنچه برای مدیریت سایت روستای خود نیاز دارید، به ترتیب منوی پنل</p>
    </div>

    <!-- منوی کامل پنل -->
    <div class="menu-list">
        <h4>🗂 منوی سمت راست پنل شامل گزینه‌های زیر است:</h4>
        <ul>
            <li>داشبورد</li>
            <li>درخواست‌های مردمی</li>
            <li>تنظیمات نمایش</li>
            <li>اخبار</li>
            <li>پروژه‌ها</li>
            <li>رویدادها</li>
            <li>گزارش عوارض</li>
            <li>امکانات</li>
            <li>شورا</li>
            <li>مصوبات شورا</li>
            <li>جاذبه‌ها</li>
            <li>فایل‌های دانلود</li>
        </ul>
    </div>

    <div class="alert-box">
        <strong>💡 نکته:</strong> تمام این گزینه‌ها از طریق <strong>منوی سمت راست پنل</strong> در دسترس هستند. با کلیک روی هر عنوان، به صفحه مربوطه می‌روید.
    </div>

    <!-- ۱. داشبورد -->
    <div class="section-card">
        <h2><span>📊</span> ۱. داشبورد</h2>
        <div class="menu-path">منو ← داشبورد</div>
        <p>اولین صفحه‌ای که پس از ورود می‌بینید. در این صفحه آمار کلی روستا نمایش داده می‌شود:</p>
        <ul>
            <li>تعداد اخبار ثبت‌شده</li>
            <li>تعداد پروژه‌ها</li>
            <li>تعداد رویدادها</li>
            <li>تعداد امکانات</li>
            <li>تعداد اعضای شورا</li>
            <li>تعداد جاذبه‌ها</li>
            <li>تعداد تصاویر گالری</li>
            <li>تعداد درخواست‌های مردمی</li>
        </ul>
        <p>در پایین همین صفحه، بخش <strong>«تنظیمات سریع»</strong> قرار دارد که می‌توانید از آنجا مستقیماً به تغییر تنظیمات نمایش بروید.</p>
    </div>

    <!-- ۲. درخواست‌های مردمی -->
    <div class="section-card">
        <h2><span>📩</span> ۲. درخواست‌های مردمی</h2>
        <div class="menu-path">منو ← درخواست‌های مردمی</div>
        <p>درخواست‌هایی که اهالی از طریق سایت ثبت می‌کنند، در این بخش نمایش داده می‌شوند.</p>
        <ul class="step-list">
            <li>روی <strong>«درخواست‌های مردمی»</strong> کلیک کنید.</li>
            <li>لیست درخواست‌ها را با کد پیگیری، نام، موضوع و تاریخ مشاهده کنید.</li>
            <li>با کلیک روی هر درخواست، جزئیات آن باز می‌شود.</li>
            <li>وضعیت درخواست را می‌توانید تغییر دهید (در انتظار بررسی / در حال بررسی / پاسخ داده شده).</li>
        </ul>
    </div>

    <!-- ۳. تنظیمات نمایش -->
    <div class="section-card">
        <h2><span>⚙️</span> ۳. تنظیمات نمایش</h2>
        <div class="menu-path">منو ← تنظیمات نمایش</div>
        <p>در این بخش مشخص می‌کنید کدام قسمت‌ها در سایت روستا نمایش داده شوند و کدام مخفی باشند.</p>
        <ul class="step-list">
            <li>تیک بخش‌هایی را که می‌خواهید در سایت دیده شوند، فعال نگه دارید.</li>
            <li>برای مخفی کردن هر بخش (مثل گالری، شورا، جاذبه‌ها و...)، تیک آن را بردارید.</li>
            <li>در پایان روی دکمه <strong>«ذخیره تغییرات»</strong> کلیک کنید.</li>
        </ul>
        <p>همچنین در این بخش می‌توانید اطلاعات کلی روستا (نام، جمعیت، دهیار، شماره تماس، عکس هدر) را نیز ویرایش کنید.</p>
    </div>

    <!-- ۴. اخبار -->
    <div class="section-card">
        <h2><span>📰</span> ۴. اخبار</h2>
        <div class="menu-path">منو ← اخبار</div>
        <p>مهم‌ترین بخش سایت شما، اخبار و اطلاعیه‌های روستا است.</p>
        <ul class="step-list">
            <li>برای ثبت خبر جدید، روی دکمه <strong>«افزودن خبر»</strong> کلیک کنید.</li>
            <li>عنوان، متن کامل، تصویر شاخص و وضعیت (منتشر شده / پیش‌نویس) را وارد کنید.</li>
            <li>اگر خبر فوری است، گزینه <strong>«سنجاق شده»</strong> را فعال کنید تا بالای همه اخبار نمایش داده شود.</li>
            <li>برای ویرایش یا حذف خبر، از دکمه‌های عملیات کنار هر خبر استفاده کنید.</li>
        </ul>
    </div>

    <!-- ۵. پروژه‌ها -->
    <div class="section-card">
        <h2><span>🏗</span> ۵. پروژه‌ها</h2>
        <div class="menu-path">منو ← پروژه‌ها</div>
        <p>پروژه‌های عمرانی و خدماتی روستا را در این بخش ثبت کنید.</p>
        <ul class="step-list">
            <li>با کلیک روی <strong>«افزودن پروژه»</strong>، عنوان، توضیحات و درصد پیشرفت پروژه را وارد کنید.</li>
            <li>می‌توانید برای هر پروژه تصویر هم آپلود کنید.</li>
            <li>وضعیت پروژه (در حال اجرا / تکمیل شده / متوقف) را مشخص کنید.</li>
        </ul>
    </div>

    <!-- ۶. رویدادها -->
    <div class="section-card">
        <h2><span>📅</span> ۶. رویدادها</h2>
        <div class="menu-path">منو ← رویدادها</div>
        <p>مناسبت‌ها، مراسم‌های مذهبی، جشن‌ها و رویدادهای فرهنگی روستا را ثبت کنید.</p>
        <ul class="step-list">
            <li>روی <strong>«افزودن رویداد»</strong> کلیک کنید.</li>
            <li>عنوان رویداد، تاریخ (شمسی) و توضیحات آن را وارد کنید.</li>
            <li>می‌توانید تصویری هم برای رویداد آپلود کنید.</li>
        </ul>
    </div>

    <!-- ۷. گزارش عوارض -->
    <div class="section-card">
        <h2><span>📊</span> ۷. گزارش عوارض</h2>
        <div class="menu-path">منو ← گزارش عوارض</div>
        <p>گزارش‌های مالی و فیش‌های عوارض صادر شده برای روستا را در این بخش مشاهده کنید.</p>
        <ul>
            <li>مشاهده فیش‌های پرداخت شده</li>
            <li>مشاهده فیش‌های در انتظار بررسی</li>
            <li>مشاهده فیش‌های رد شده</li>
        </ul>
    </div>

    <!-- ۸. امکانات -->
    <div class="section-card">
        <h2><span>🏢</span> ۸. امکانات</h2>
        <div class="menu-path">منو ← امکانات</div>
        <p>لیست امکانات موجود در روستا (مدرسه، مسجد، خانه بهداشت، آب، برق، گاز و...) را در این بخش ثبت کنید.</p>
        <ul class="step-list">
            <li>برای هر امکان، یک عنوان (مثلاً «مدرسه ابتدایی») و توضیحات کوتاه وارد کنید.</li>
            <li>می‌توانید برای هر امکان یک آیکون یا تصویر هم انتخاب کنید.</li>
        </ul>
    </div>

    <!-- ۹. شورا -->
    <div class="section-card">
        <h2><span>👥</span> ۹. شورا</h2>
        <div class="menu-path">منو ← شورا</div>
        <p>اطلاعات اعضای شورای اسلامی روستا را در این بخش ثبت کنید.</p>
        <ul class="step-list">
            <li>برای هر عضو، نام و نام خانوادگی، سمت (رئیس / نایب‌رئیس / عضو) و شماره تماس را وارد کنید.</li>
            <li>می‌توانید برای هر عضو تصویر پروفایل هم آپلود کنید.</li>
            <li>امکان ویرایش و حذف اعضا از طریق دکمه‌های عملیات وجود دارد.</li>
        </ul>
    </div>

    <!-- ۱۰. مصوبات شورا -->
    <div class="section-card">
        <h2><span>📜</span> ۱۰. مصوبات شورا</h2>
        <div class="menu-path">منو ← مصوبات شورا</div>
        <p>صورت‌جلسات و مصوبات شورای اسلامی را برای اطلاع عموم منتشر کنید.</p>
        <ul class="step-list">
            <li>شماره صورت‌جلسه، تاریخ و خلاصه مصوبات را وارد کنید.</li>
            <li>می‌توانید فایل PDF صورت‌جلسه را هم آپلود کنید تا کاربران دانلود کنند.</li>
        </ul>
    </div>

    <!-- ۱۱. جاذبه‌ها -->
    <div class="section-card">
        <h2><span>🏛</span> ۱۱. جاذبه‌ها</h2>
        <div class="menu-path">منو ← جاذبه‌ها</div>
        <p>مکان‌های دیدنی، امامزاده‌ها، آثار تاریخی و جاذبه‌های گردشگری روستا را معرفی کنید.</p>
        <ul class="step-list">
            <li>نام مکان، توضیحات و تصویر آن را وارد کنید.</li>
            <li>موقعیت مکانی (طول و عرض جغرافیایی) نیز اختیاری است.</li>
        </ul>
    </div>

    <!-- ۱۲. فایل‌های دانلود -->
    <div class="section-card">
        <h2><span>📁</span> ۱۲. فایل‌های دانلود</h2>
        <div class="menu-path">منو ← فایل‌های دانلود</div>
        <p>فایل‌های مهم روستا (مثل فرم‌ها، بخشنامه‌ها، اسناد و...) را برای دانلود کاربران قرار دهید.</p>
        <ul class="step-list">
            <li>روی <strong>«افزودن فایل»</strong> کلیک کنید.</li>
            <li>عنوان فایل، توضیحات کوتاه و خود فایل (PDF، Word، Excel و...) را انتخاب کنید.</li>
        </ul>
    </div>

    <!-- دکمه‌های بالای پنل -->
    <div class="section-card" style="border-right-color: #27689f;">
        <h2><span>🔝</span> دکمه‌های بالای پنل</h2>
        <p>در بالای صفحه پنل، چند دکمه مهم وجود دارد:</p>
        <ul>
            <li><strong>مشاهده سایت:</strong> سایت روستا را در تب جدید باز می‌کند تا تغییرات را ببینید.</li>
            <li><strong>داشبورد کل:</strong> شما را به داشبورد اصلی پنل برمی‌گرداند.</li>
            <li><strong>تغییر رمز:</strong> برای تغییر رمز عبور حساب کاربری استفاده کنید.</li>
            <li><strong>خروج:</strong> از حساب کاربری خارج می‌شوید.</li>
        </ul>
    </div>

    <div class="alert-box" style="background:#fdeceb; border-color:#b3261e;">
        <strong style="color:#b3261e;">⚠️ نکات امنیتی مهم:</strong>
        <ul style="margin-bottom:0; margin-top:10px;">
            <li>هرگز نام کاربری و رمز عبور خود را در اختیار دیگران قرار ندهید.</li>
            <li>پس از اتمام کار، حتماً از حساب کاربری <strong>خارج</strong> شوید.</li>
            <li>در صورت مشاهده هرگونه فعالیت مشکوک، فوراً به مدیر سامانه اطلاع دهید.</li>
        </ul>
    </div>

    <p style="text-align:center; color:#888; font-size:12px; margin-top:40px; border-top:1px solid #f5efe4; padding-top:20px;">
        این راهنما در تاریخ ۱۴۰۵/۰۸/۲۳ تهیه شده است. | پشتیبانی: dr.zamani1368@gmail.com
    </p>
</div>
@endsection
