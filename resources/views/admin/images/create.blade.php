<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آپلود تصاویر {{ $village->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .header { background: linear-gradient(135deg, #2e7d32, #1b5e20); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .header h1 { margin: 0; font-size: 19px; }
        .header a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; }

        .container { max-width: 700px; margin: auto; padding: 30px 20px; }

        .card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }

        .error { background: #ffcdd2; color: #c62828; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; }

        .field { margin-bottom: 20px; }
        .field label { display: block; margin-bottom: 8px; font-size: 13px; color: #546e7a; font-weight: 600; }
        .field input[type="text"], .field input[type="file"] {
            width: 100%; padding: 12px 14px; border: 1px solid #cfd8dc; border-radius: 8px;
            font-family: inherit; font-size: 14px; outline: none; background: white;
        }
        .field input:focus { border-color: #2e7d32; }
        .hint { font-size: 12px; color: #90a4ae; margin-top: 6px; }

        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border-radius: 8px; text-decoration: none; font-family: inherit; font-size: 14px; cursor: pointer; border: none; }
        .btn-primary { background: #2e7d32; color: white; }
        .btn-primary:hover { background: #1b5e20; }
        .btn-secondary { background: #cfd8dc; color: #37474f; }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>
<body>

<div class="header">
    <h1>📤 آپلود تصاویر: {{ $village->name }}</h1>
    <a href="{{ route('admin.village.images.index', $village->id) }}">← گالری</a>
</div>

<div class="container">

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('admin.village.images.store', $village->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label>انتخاب تصاویر *</label>
                <input type="file" name="images[]" accept="image/*" multiple required>
                <div class="hint">می‌تونی همزمان چند تا تصویر انتخاب کنی. حداکثر ۵ مگابایت هر تصویر.</div>
            </div>

            <div class="field">
                <label>توضیح برای همه تصاویر (اختیاری)</label>
                <input type="text" name="caption" value="{{ old('caption') }}" placeholder="مثلاً: منظره روستا">
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">📤 آپلود</button>
                <a href="{{ route('admin.village.images.index', $village->id) }}" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>

</div>

</body>
</html>
