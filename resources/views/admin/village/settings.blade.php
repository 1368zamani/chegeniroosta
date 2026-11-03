@extends('admin.village.layout')

@section('title', 'تنظیمات نمایش')

@section('content')

<style>
/* ═══ CHECKBOX-FIX-FINAL ═══ */

/* پاک کردن هر hover قبلی */
.checkbox-field,
.checkbox-field:hover,
.checkbox-field:focus,
.checkbox-field:active {
    background: #ffffff !important;
    color: #2e7d32 !important;
    padding: 14px 18px !important;
    margin-bottom: 10px !important;
    border-radius: 10px !important;
    border: 1.5px solid #e8f5e9 !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    transition: none !important;
    cursor: pointer !important;
}

/* فقط border عوض شه، نه background */
.checkbox-field:hover {
    border-color: #2e7d32 !important;
    box-shadow: 0 2px 8px rgba(21,101,192,0.12) !important;
}

/* متن داخل label همیشه آبی */
.checkbox-field label,
.checkbox-field span,
.checkbox-field * {
    color: #2e7d32 !important;
    background: transparent !important;
    font-weight: 700 !important;
}

/* چک‌باکس آبی */
.checkbox-field input[type="checkbox"] {
    width: 20px !important;
    height: 20px !important;
    accent-color: #2e7d32 !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
}

/* اطمینان از کارت سفید */
.card {
    background: #ffffff !important;
    color: #2e7d32 !important;
}

.card h3 {
    color: #1b5e20 !important;
    background: transparent !important;
}
</style>


<style>
/* ═══ SETTINGS-FORCE-WHITE ═══ */
.card,
.card > *,
.card h3,
.card p,
.card div,
.card label,
.checkbox-field,
.checkbox-field:hover,
.checkbox-field label,
.checkbox-field span,
.settings-page,
.settings-page * {
    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;
    color: #2e7d32 !important;
}

.card {
    border: 1px solid #e8f5e9 !important;
    box-shadow: 0 2px 10px rgba(21,101,192,0.06) !important;
}

.card h3 {
    color: #1b5e20 !important;
    border-bottom: 2px solid #e8f5e9 !important;
    font-weight: 800 !important;
}

.checkbox-field {
    padding: 12px 16px !important;
    margin-bottom: 8px !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}

.checkbox-field:hover {
    background: #e8f5e9 !important;
}

.checkbox-field input[type="checkbox"] {
    width: 20px !important;
    height: 20px !important;
    accent-color: #2e7d32 !important;
    cursor: pointer !important;
}

label, span {
    color: #2e7d32 !important;
    font-weight: 700 !important;
}

input[type="text"],
input[type="email"],
input[type="number"],
select,
textarea {
    background: #ffffff !important;
    color: #2e7d32 !important;
    border: 1.5px solid #2e7d32 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
}

input:focus, select:focus, textarea:focus {
    outline: none !important;
    border-color: #1b5e20 !important;
    box-shadow: 0 0 0 3px rgba(21,101,192,0.15) !important;
}

.btn-primary {
    background: #2e7d32 !important;
    color: #fff !important;
}

.btn-success {
    background: #10b981 !important;
    color: #fff !important;
}
</style>


<div class="page-title">
    <h2>⚙️ تنظیمات نمایش {{ $village->name }}</h2>
</div>

{{-- ═══ عکس دهیار ═══ --}}
    <div style="background:#f1f8e9;border:2px solid #2e7d32;border-radius:14px;padding:18px;margin-bottom:20px">
        <h3 style="margin:0 0 12px;font-size:15px;color:#2e7d32;font-weight:800">📸 عکس دهیار</h3>

        @if(session('success'))
            <div style="background:#e8f5e9;color:#2e7d32;padding:10px 14px;border-radius:9px;margin-bottom:12px;font-weight:700">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:9px;margin-bottom:12px;font-weight:700">⚠️ {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:9px;margin-bottom:12px;font-weight:700">
                @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
            </div>
        @endif

        <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">
            <div style="flex-shrink:0">
                @if($village->dehyar_photo && Storage::disk('public')->exists($village->dehyar_photo))
                    <img src="{{ Storage::disk('public')->url($village->dehyar_photo) }}"
                         alt="{{ $village->dehyar_name }}"
                         style="width:140px;height:140px;border-radius:50%;object-fit:cover;border:4px solid #2e7d32;box-shadow:0 4px 14px rgba(21,101,192,0.3)">
                @else
                    <div style="width:140px;height:140px;border-radius:50%;background:#e8f5e9;display:flex;align-items:center;justify-content:center;font-size:50px;color:#90a4ae;border:4px solid #2e7d32">👤</div>
                @endif
            </div>

            <div style="flex:1;min-width:280px">
                <form method="POST" action="{{ route('admin.village.dehyar.photo.upload', $village->id) }}"
                      enctype="multipart/form-data"
                      style="display:flex;flex-direction:column;gap:10px">
                    @csrf
                    <label style="font-weight:700;color:#37474f;font-size:13px">انتخاب عکس (JPG / PNG / WEBP — حداکثر ۵ مگابایت)</label>
                    <input type="file" name="dehyar_photo" accept=".jpg,.jpeg,.png,.webp" required
                           style="padding:10px;border:1.5px solid #cfd8dc;border-radius:8px;background:#fff;font-family:inherit">
                    <button type="submit"
                            style="padding:11px 22px;background:#2e7d32;color:#fff;border:none;border-radius:9px;font-weight:800;cursor:pointer;font-family:inherit;align-self:flex-start">
                        📤 آپلود عکس
                    </button>
                </form>

                @if($village->dehyar_photo)
                    <form method="POST" action="{{ route('admin.village.dehyar.photo.delete', $village->id) }}"
                          onsubmit="return confirm('حذف عکس دهیار؟')" style="margin-top:10px">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="padding:9px 18px;background:#D32F2F;color:#fff;border:none;border-radius:9px;font-weight:800;cursor:pointer;font-family:inherit">
                            🗑 حذف عکس فعلی
                        </button>
                    </form>
                @endif
            </div>
        </div>
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
