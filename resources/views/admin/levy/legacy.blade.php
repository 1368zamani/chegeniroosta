@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'ماژول قدیمی عوارض')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-slate-200 p-8 text-center">
        <div class="text-4xl mb-4">📁</div>
        <h1 class="text-xl font-bold text-slate-800 mb-3">ماژول قدیمی عوارض بایگانی شد</h1>
        <p class="text-sm text-slate-600 leading-7 mb-5">
            ساختار قدیمی تعرفه و عوارض بر اساس فایل اکسل مرجع بازطراحی شد و با زیرسامانه جامع
            «عوارض احداث بنا» جایگزین گردید. لطفاً از مسیر جدید برای ثبت و مشاهده اطلاعات هر روستا استفاده کنید.
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('admin.levy.dashboard') }}" class="px-4 py-2 rounded bg-slate-800 text-white text-sm hover:bg-slate-900">رفتن به داشبورد عوارض</a>
            <a href="{{ url('/') }}" class="px-4 py-2 rounded bg-slate-100 text-slate-700 text-sm hover:bg-slate-200">صفحه اصلی سامانه</a>
        </div>
    </div>
</div>
@endsection
