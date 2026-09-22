<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنظیمات سایت</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }

        .header {
            background: linear-gradient(135deg, #0d2e4f, #103d68);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header h1 { margin: 0; font-size: 19px; }
        .header .nav { display: flex; gap: 10px; flex-wrap: wrap; }
        .header .nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            font-size: 13px;
        }
        .header .nav a:hover { background: rgba(255,255,255,0.25); }

        .container { max-width: 1000px; margin: auto; padding: 30px 20px; }

        .success {
            background: #c8e6c9;
            color: #2e7d32;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .error {
            background: #ffcdd2;
            color: #c62828;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #0d2e4f, #1565c0);
            color: white;
            padding: 18px 24px;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body { padding: 24px; }

        .field { margin-bottom: 20px; }
        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            color: #546e7a;
            font-weight: 600;
        }
        .field input[type="text"],
        .field input[type="file"],
        .field textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cfd8dc;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }
        .field input:focus,
        .field textarea:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 3px rgba(21,101,192,0.1);
        }
        .field textarea {
            min-height: 160px;
            resize: vertical;
            line-height: 2;
            direction: rtl;
        }
        .hint {
            font-size: 12px;
            color: #90a4ae;
            margin-top: 6px;
            line-height: 1.7;
        }
        .hint code {
            background: #f5f7fa;
            padding: 2px 6px;
            border-radius: 4px;
            direction: ltr;
            display: inline-block;
            font-family: monospace;
        }

        .preview-img {
            max-height: 80px;
            max-width: 200px;
            border-radius: 8px;
            margin-top: 10px;
            display: block;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .actions {
            display: flex;
            gap: 12px;
            position: sticky;
            bottom: 20px;
            background: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            z-index: 10;
            border: 2px solid #e3f2fd;
        }

        .btn {
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0d2e4f, #1565c0);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(13,46,79,0.3); }
        .btn-secondary { background: #cfd8dc; color: #37474f; }
        .btn-secondary:hover { background: #b0bec5; }

        @media (max-width: 700px) {
            .container { padding: 20px 15px; }
            .card-body { padding: 18px; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>⚙️ تنظیمات سایت</h1>
    <div class="nav">
        <a href="{{ route('admin.dashboard') }}">داشبورد</a>
        <a href="/" target="_blank">🌐 مشاهده سایت</a>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $err)
                <div>❌ {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- ============ هدر ============ --}}
        <div class="card">
            <div class="card-header">🏛 هدر سایت</div>
            <div class="card-body">

                <div class="field">
                    <label>عنوان اصلی هدر</label>
                    <input type="text" name="header_title" value="{{ old('header_title', $settings['header_title']) }}">
                </div>

                <div class="field">
                    <label>زیرعنوان</label>
                    <input type="text" name="header_subtitle" value="{{ old('header_subtitle', $settings['header_subtitle']) }}">
                </div>

                <div class="field">
                    <label>آرم (لوگو)</label>
                    <input type="file" name="header_emblem_file" accept="image/*">
                    <div class="hint">
                        فایل فعلی: <code>{{ $settings['header_emblem'] }}</code>
                    </div>
                    @if($settings['header_emblem'])
                        <img src="{{ asset('storage/' . $settings['header_emblem']) }}" class="preview-img" onerror="this.src='{{ asset($settings['header_emblem']) }}'">
                    @endif
                </div>

            </div>
        </div>

        {{-- ============ نوار وفاق ملی ============ --}}
        <div class="card">
            <div class="card-header">✦ نوار دولت وفاق ملی</div>
            <div class="card-body">

                <div class="field">
                    <label>متن نوار</label>
                    <input type="text" name="banner_text" value="{{ old('banner_text', $settings['banner_text']) }}">
                    <div class="hint">این متن زیر منوی ناوبری نمایش داده می‌شود.</div>
                </div>

                <div class="field">
                    <label>کلمه اول طلایی</label>
                    <input type="text" name="banner_highlight_1" value="{{ old('banner_highlight_1', $settings['banner_highlight_1']) }}">
                </div>

                <div class="field">
                    <label>کلمه دوم طلایی</label>
                    <input type="text" name="banner_highlight_2" value="{{ old('banner_highlight_2', $settings['banner_highlight_2']) }}">
                </div>

            </div>
        </div>

        {{-- ============ اشعار ============ --}}
        <div class="card">
            <div class="card-header">📜 اشعار روزانه (نوار بالای هدر)</div>
            <div class="card-body">

                <div class="field">
                    <label>لیست اشعار</label>
                    <textarea name="poetry_verses" dir="rtl" style="font-family: Tahoma;">@foreach($settings['poetry_verses'] as $v){{ $v['text'] ?? '' }}|||{{ $v['poet'] ?? '' }}
@endforeach</textarea>
                    <div class="hint">
                        <strong>فرمت:</strong> هر خط = یک بیت<br>
                        <code>متن بیت|||نام شاعر</code><br>
                        <strong>مثال:</strong><br>
                        <code>توانا بود هر که دانا بود / ز دانش دل پیر برنا بود|||فردوسی</code><br>
                        <br>
                        هر روز یک بیت به صورت چرخشی نمایش داده می‌شود.
                    </div>
                </div>

            </div>
        </div>

        {{-- ============ فوتر ============ --}}
        <div class="card">
            <div class="card-header">📋 فوتر سایت</div>
            <div class="card-body">

                <div class="field">
                    <label>درباره سامانه</label>
                    <textarea name="footer_about" style="min-height:100px;">{{ old('footer_about', $settings['footer_about']) }}</textarea>
                </div>

                <div class="field">
                    <label>لینک‌های مفید</label>
                    <textarea name="footer_links" dir="ltr" style="font-family: monospace; min-height:120px; direction: ltr;">@foreach($settings['footer_links'] as $l){{ $l['title'] ?? '' }}|||{{ $l['url'] ?? '' }}
@endforeach</textarea>
                    <div class="hint">
                        <strong>فرمت:</strong> هر خط = یک لینک<br>
                        <code>عنوان|||آدرس</code><br>
                        <strong>مثال:</strong><br>
                        <code>وزارت کشور|||https://www.moi.ir</code>
                    </div>
                </div>

                <div class="field">
                    <label>متن کپی‌رایت</label>
                    <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright']) }}">
                </div>

            </div>
        </div>

        {{-- ============ دکمه‌ها ============ --}}
        <div class="actions">
            <button type="submit" class="btn btn-primary">💾 ذخیره همه تغییرات</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">انصراف</a>
        </div>

    </form>

</div>

</body>
</html>
