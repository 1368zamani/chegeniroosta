@extends('admin.village.layout')

@section('title', 'تنظیمات نمایش')

@section('content')

<div class="page-title">
    <h2>⚙️ تنظیمات نمایش {{ $village->name }}</h2>
</div>

<form method="POST" action="{{ route('admin.village.settings.update', $village->id) }}">
    @csrf

    <div class="card">
        <h3>👁 نمایش بخش‌ها در سایت</h3>
        <p style="color:#546e7a; font-size:13px; margin:0 0 16px;">تیک بزنید تا بخش مربوطه در سایت روستا نمایش داده شود.</p>

        <label class="checkbox-field">
            <input type="checkbox" name="show_stats" value="1" {{ $village->show_stats ? 'checked' : '' }}>
            <span>📊 کارت‌های آمار (پروژه، درخواست، ...)</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_news" value="1" {{ $village->show_news ? 'checked' : '' }}>
            <span>📰 اخبار و اطلاعیه‌ها</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_projects" value="1" {{ $village->show_projects ? 'checked' : '' }}>
            <span>🏗 پروژه‌های عمرانی</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_events" value="1" {{ $village->show_events ? 'checked' : '' }}>
            <span>📅 رویدادهای پیش‌رو</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_facilities" value="1" {{ $village->show_facilities ? 'checked' : '' }}>
            <span>🏛 امکانات روستا</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_council" value="1" {{ $village->show_council ? 'checked' : '' }}>
            <span>👥 شورای اسلامی روستا</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_attractions" value="1" {{ $village->show_attractions ? 'checked' : '' }}>
            <span>🏞 جاذبه‌های گردشگری</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_gallery" value="1" {{ $village->show_gallery ? 'checked' : '' }}>
            <span>🖼 گالری تصاویر</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_request_box" value="1" {{ $village->show_request_box ? 'checked' : '' }}>
            <span>📩 باکس درخواست مردمی</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_about" value="1" {{ $village->show_about ? 'checked' : '' }}>
            <span>📝 درباره روستا (معرفی)</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="show_info" value="1" {{ $village->show_info ? 'checked' : '' }}>
            <span>📋 اطلاعات روستا</span>
        </label>
    </div>

    <div class="card">
        <h3>📝 اطلاعات پایه</h3>

        <div class="field">
            <label>متن خوش‌آمدگویی</label>
            <input type="text" name="welcome_text" value="{{ old('welcome_text', $village->welcome_text) }}" placeholder="به روستای ... خوش آمدید">
        </div>

        <div class="field">
            <label>توضیحات (خلاصه معرفی)</label>
            <textarea name="description">{{ old('description', $village->description) }}</textarea>
        </div>

        <div class="field">
            <label>تاریخچه روستا</label>
            <textarea name="history_text">{{ old('history_text', $village->history_text) }}</textarea>
        </div>

        <div class="field">
            <label>وجه تسمیه (داستان نام)</label>
            <textarea name="naming_story">{{ old('naming_story', $village->naming_story) }}</textarea>
        </div>
    </div>

    <div class="card">
        <h3>📞 اطلاعات تماس و دهیاری</h3>

        <div class="row-2">
            <div class="field">
                <label>نام دهیار</label>
                <input type="text" name="dehyar_name" value="{{ old('dehyar_name', $village->dehyar_name) }}">
            </div>
            <div class="field">
                <label>ساعات پاسخگویی</label>
                <input type="text" name="dehyar_hours" value="{{ old('dehyar_hours', $village->dehyar_hours) }}" placeholder="مثلاً: ۸ صبح تا ۲ بعدازظهر">
            </div>

<div class="field">
    <label>لینک اینستاگرام دهیار</label>
    <input type="text" name="dehyar_instagram" value="{{ old('dehyar_instagram', $village->dehyar_instagram) }}" dir="ltr" placeholder="مثال: dehyar_abuzar">
    <div class="hint">بدون @ — نام کاربری یا آدرس کامل اینستاگرام</div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="field">
        <label>شماره تماس دهیار</label>
        <input type="text" name="dehyar_phone" value="{{ old('dehyar_phone', $village->dehyar_phone) }}" dir="ltr" style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
    </div>
    <div class="field">
        <label>ایمیل دهیار</label>
        <input type="email" name="dehyar_email" value="{{ old('dehyar_email', $village->dehyar_email) }}" dir="ltr" style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
    </div>
</div>

<div class="field">
    <label>درباره دهیار (بیوگرافی)</label>
    <textarea name="dehyar_bio" style="width:100%;min-height:100px;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;resize:vertical;" placeholder="توضیح کوتاه درباره دهیار...">{{ old('dehyar_bio', $village->dehyar_bio) }}</textarea>
</div>

<div class="field">
    <label>تحصیلات دهیار</label>
    <textarea name="dehyar_education" style="width:100%;min-height:80px;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;resize:vertical;" placeholder="مثال: کارشناسی مدیریت...">{{ old('dehyar_education', $village->dehyar_education) }}</textarea>
</div>

<div class="field">
    <label>سوابق شغلی دهیار</label>
    <textarea name="dehyar_experience" style="width:100%;min-height:100px;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;resize:vertical;" placeholder="مثال: دهیار ... از سال ...">{{ old('dehyar_experience', $village->dehyar_experience) }}</textarea>
</div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>تلفن روستا</label>
                <input type="text" name="phone" value="{{ old('phone', $village->phone) }}" dir="ltr">
            </div>
            <div class="field">
                <label>تلفن شورا</label>
                <input type="text" name="council_phone" value="{{ old('council_phone', $village->council_phone) }}" dir="ltr">
            </div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>ایمیل</label>
                <input type="email" name="email" value="{{ old('email', $village->email) }}" dir="ltr">
            </div>
            <div class="field">
                <label>آدرس</label>
                <input type="text" name="address" value="{{ old('address', $village->address) }}">
            </div>
        </div>
    </div>

    <div class="card">
        <h3>💳 اطلاعات پرداخت (اختیاری)</h3>
        <p style="color:#78909c; font-size:12.5px; margin:0 0 16px;">این اطلاعات در قسمت پرداخت عوارض نمایش داده می‌شود.</p>

        <div class="row-2">
            <div class="field">
                <label>نام بانک</label>
                <input type="text" name="bank_name" value="{{ old('bank_name', $village->bank_name) }}">
            </div>
            <div class="field">
                <label>شماره حساب</label>
                <input type="text" name="bank_account" value="{{ old('bank_account', $village->bank_account) }}" dir="ltr">
            </div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>عرض جغرافیایی (Latitude)</label>
                <input type="text" name="latitude" value="{{ old('latitude', $village->latitude) }}" dir="ltr" placeholder="مثال: 33.49857115">
                <small style="color:#64748b; font-size:12px;">از گوگل مپ، روی روستا کلیک راست کنید → مختصات را کپی کنید</small>
            </div>
            <div class="field">
                <label>طول جغرافیایی (Longitude)</label>
                <input type="text" name="longitude" value="{{ old('longitude', $village->longitude) }}" dir="ltr" placeholder="مثال: 48.02885012">
                <small style="color:#64748b; font-size:12px;">اولین عدد = lat، دومین عدد = lng</small>
            </div>
        </div>

        <div class="field">
            <label>شناسه پرداخت</label>
            <input type="text" name="payment_id" value="{{ old('payment_id', $village->payment_id) }}" dir="ltr">
        </div>
    </div>

    <div style="display:flex; gap:12px; margin-top:20px;">
        <button type="submit" class="btn btn-success">💾 ذخیره تنظیمات</button>
        <a href="{{ route('admin.village.dashboard', $village->id) }}" class="btn btn-secondary">انصراف</a>
    </div>
</form>

@endsection
