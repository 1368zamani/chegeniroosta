<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>افزودن روستای جدید</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .header { background: linear-gradient(135deg, #1565c0, #0d47a1); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .header h1 { margin: 0; font-size: 19px; }
        .header a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; }
        .container { max-width: 900px; margin: auto; padding: 30px 20px; }
        .card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .card h2 { margin: 0 0 20px; font-size: 17px; color: #1565c0; border-bottom: 2px solid #e3f2fd; padding-bottom: 12px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .field { margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 6px; font-size: 13px; color: #546e7a; font-weight: 600; }
        .field input, .field select, .field textarea { width: 100%; padding: 11px 14px; border: 1px solid #cfd8dc; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; background: white; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color: #1565c0; }
        .field textarea { min-height: 90px; resize: vertical; }
        .hint { font-size: 12px; color: #90a4ae; margin-top: 5px; }
        .checkbox { display: flex; align-items: center; gap: 10px; }
        .checkbox input { width: 20px; height: 20px; }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border-radius: 8px; text-decoration: none; font-family: inherit; font-size: 14px; cursor: pointer; border: none; }
        .btn-primary { background: #1565c0; color: white; }
        .btn-primary:hover { background: #0d47a1; }
        .btn-secondary { background: #cfd8dc; color: #37474f; }
        .btn-secondary:hover { background: #b0bec5; }
        .error { background: #ffcdd2; color: #c62828; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; }
        @media (max-width: 700px) { .row { grid-template-columns: 1fr; } }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>

<div class="header">
    <h1>افزودن روستای جدید</h1>
    <a href="{{ route('admin.dashboard') }}">← بازگشت به داشبورد</a>
</div>

<div class="container">

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.villages.store') }}">
        @csrf

        <div class="card">
            <h2>📌 اطلاعات اصلی</h2>

            <div class="row">
                <div class="field">
                    <label>نام روستا *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="field">
                    <label>Slug (انگلیسی، برای URL) *</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required style="direction:ltr;" placeholder="mesal: sabzevar">
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label>بخش *</label>
                    <select name="section_id" required>
                        <option value="">— انتخاب بخش —</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ old('section_id', $defaultSection) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>دهستان</label>
                    <input type="text" name="district" value="{{ old('district') }}">
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label>Subdomain (اختیاری)</label>
                    <input type="text" name="subdomain" value="{{ old('subdomain') }}" style="direction:ltr;" placeholder="مثلاً: sabzevar">
                    <div class="hint">اگر پر کنی، روستا یه سایت اختصاصی می‌گیره: <span dir="ltr">subdomain.chegeniroosta.ir</span></div>
                </div>
                <div class="field">
                    <label>شهرستان</label>
                    <input type="text" name="county" value="{{ old('county', 'چگنی') }}">
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label>استان</label>
                    <input type="text" name="province" value="{{ old('province', 'لرستان') }}">
                </div>
                <div class="field">
                    <label>تلفن روستا</label>
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="field">
                <label>آدرس</label>
                <input type="text" name="address" value="{{ old('address') }}">
            </div>

            <div class="field">
                <label>ایمیل</label>
                <input type="email" name="email" value="{{ old('email') }}" style="direction:ltr;">
            </div>

            <div class="field">
                <label>توضیحات</label>
                <textarea name="description">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="card">
            <h2>📊 آمار و اطلاعات تکمیلی</h2>

            <div class="row">
                <div class="field">
                    <label>تعداد خانوار</label>
                    <input type="number" name="households" value="{{ old('households') }}" min="0">
                </div>
                <div class="field">
                    <label>جمعیت</label>
                    <input type="number" name="population" value="{{ old('population') }}" min="0">
                </div>
            </div>

            <div class="field">
                <label>مساحت (هکتار)</label>
                <input type="number" step="0.01" name="area" value="{{ old('area') }}" min="0">
            </div>

            <div class="row">
                <div class="field">
                    <label>نام دهیار</label>
                    <input type="text" name="dehyar_name" value="{{ old('dehyar_name') }}">
                </div>

                <div class="field">
                    <label>لینک اینستاگرام دهیار</label>
                    <input type="text" name="dehyar_instagram" value="{{ old('dehyar_instagram') }}" dir="ltr" placeholder="مثال: dehyar_abuzar">
                    <div class="hint">بدون @ — نام کاربری یا آدرس کامل</div>
                </div>
                <div class="field">
                    <label>نام رئیس شورا</label>
                    <input type="text" name="council_head_name" value="{{ old('council_head_name') }}">
                </div>
            </div>

            <div class="field checkbox">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" style="margin:0; cursor:pointer;">روستا فعال است (نمایش در سایت)</label>
            </div>
        </div>

        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-primary">💾 ذخیره روستا</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">انصراف</a>
        </div>

    </form>

</div>

</body>
</html>
