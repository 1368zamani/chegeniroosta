<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ویرایش {{ $section->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .header { background: #1565c0; color: white; padding: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .success { background: #c8e6c9; color: #2e7d32; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; }
        .error { background: #ffcdd2; color: #c62828; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; }
        .card { background: white; border-radius: 12px; padding: 28px; margin-bottom: 20px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
        .card h2 { margin: 0 0 20px; font-size: 17px; color: #1565c0; border-bottom: 2px solid #e3f2fd; padding-bottom: 12px; }
        .field { margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 8px; font-size: 13px; color: #546e7a; font-weight: 600; }
        .field input[type="text"],
        .field input[type="email"],
        .field textarea { width: 100%; padding: 12px 14px; border: 1px solid #cfd8dc; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; }
        .field input:focus, .field textarea:focus { border-color: #1565c0; }
        .field textarea { resize: vertical; min-height: 90px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .checkbox { display: flex; align-items: center; gap: 10px; }
        .checkbox input { width: 20px; height: 20px; }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 8px; font-family: inherit; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #1565c0; color: white; }
        .btn-primary:hover { background: #0d47a1; }
        .btn-secondary { background: #cfd8dc; color: #37474f; }
        .btn-secondary:hover { background: #b0bec5; }
        .btn-danger { background: #e53935; color: white; }
        .btn-danger:hover { background: #c62828; }
        .photo-preview { display: flex; align-items: center; gap: 20px; }
        .photo-preview img { width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #e3f2fd; }
        .photo-preview .empty { width: 130px; height: 130px; border-radius: 50%; background: #cfd8dc; display: flex; align-items: center; justify-content: center; font-size: 44px; color: #90a4ae; }
        .photo-actions { display: flex; flex-direction: column; gap: 10px; }
        .file-input { font-size: 13px; }
        .hint { font-size: 12px; color: #90a4ae; margin-top: 6px; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { display: inline-block; padding: 10px 18px; background: white; color: #1565c0; text-decoration: none; border-radius: 8px; margin-right: 10px; font-size: 14px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); }
        .nav-links a:hover { background: #1565c0; color: white; }
        @media (max-width: 700px) {
            .row { grid-template-columns: 1fr; }
            .photo-preview { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>ویرایش بخش: {{ $section->name }}</h1>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <div class="nav-links">
        <a href="{{ route('admin.sections.index') }}">← لیست بخش‌ها</a>
        <a href="/">مشاهده سایت</a>
    </div>

    {{-- فرم اطلاعات بخش --}}
    <form method="POST" action="{{ route('admin.sections.update', $section->id) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <h2>📌 اطلاعات بخش</h2>

            <div class="row">
                <div class="field">
                    <label>نام بخش</label>
                    <input type="text" name="name" value="{{ old('name', $section->name) }}" required>
                </div>
                <div class="field">
                    <label>Subdomain</label>
                    <input type="text" name="subdomain" value="{{ old('subdomain', $section->subdomain) }}">
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label>شهرستان</label>
                    <input type="text" name="county" value="{{ old('county', $section->county) }}">
                </div>
                <div class="field">
                    <label>استان</label>
                    <input type="text" name="province" value="{{ old('province', $section->province) }}">
                </div>
            </div>

            <div class="field">
                <label>توضیحات</label>
                <textarea name="description">{{ old('description', $section->description) }}</textarea>
            </div>

            <div class="row">
                <div class="field">
                    <label>آدرس</label>
                    <input type="text" name="address" value="{{ old('address', $section->address) }}">
                </div>
                <div class="field">
                    <label>تلفن بخشداری</label>
                    <input type="text" name="phone" value="{{ old('phone', $section->phone) }}">
                </div>
            </div>

            <div class="field">
                <label>ایمیل بخشداری</label>
                <input type="email" name="email" value="{{ old('email', $section->email) }}">
            </div>

            <div class="field checkbox">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
                <label for="is_active" style="margin:0; cursor:pointer;">بخش فعال است</label>
            </div>
        </div>

        <div class="card">
            <h2>👤 اطلاعات بخشدار</h2>

            <div class="field">
                <label>نام و نام خانوادگی بخشدار</label>
                <input type="text" name="bakhshdar_name" value="{{ old('bakhshdar_name', $section->bakhshdar_name) }}">
            </div>

            <div class="field">
                <label>بیوگرافی</label>
                <textarea name="bakhshdar_bio" placeholder="توضیح کوتاه درباره بخشدار...">{{ old('bakhshdar_bio', $section->bakhshdar_bio) }}</textarea>
            </div>

            <div class="row">
                <div class="field">
                    <label>تلفن دفتر بخشدار</label>
                    <input type="text" name="bakhshdar_phone" value="{{ old('bakhshdar_phone', $section->bakhshdar_phone) }}">
                </div>
                <div class="field">
                    <label>ایمیل بخشدار</label>
                    <input type="email" name="bakhshdar_email" value="{{ old('bakhshdar_email', $section->bakhshdar_email) }}">
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">💾 ذخیره تغییرات</button>
                <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">انصراف</a>
            </div>
        </div>
    </form>

    {{-- فرم آپلود عکس (جدا) --}}
    <div class="card">
        <h2>🖼 عکس بخشدار</h2>

        <div class="photo-preview">
            @if($section->bakhshdar_photo)
                <img src="{{ asset('storage/' . $section->bakhshdar_photo) }}" alt="">
            @else
                <div class="empty">👤</div>
            @endif

            <div class="photo-actions">
                <form method="POST" action="{{ route('admin.sections.uploadPhoto', $section->id) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="photo" accept="image/*" class="file-input" required>
                    <div class="hint">فرمت JPG یا PNG — حداکثر ۵ مگابایت</div>
                    <button type="submit" class="btn btn-primary" style="margin-top:10px;">📤 آپلود عکس</button>
                </form>

                @if($section->bakhshdar_photo)
                    <form method="POST" action="{{ route('admin.sections.deletePhoto', $section->id) }}" onsubmit="return confirm('حذف عکس بخشدار؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">🗑 حذف عکس</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>

</body>
</html>
