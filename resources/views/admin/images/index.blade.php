<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گالری تصاویر {{ $village->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .header { background: linear-gradient(135deg, #2e7d32, #1b5e20); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .header h1 { margin: 0; font-size: 19px; }
        .header .nav { display: flex; gap: 10px; }
        .header a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; }
        .header a:hover { background: rgba(255,255,255,0.25); }

        .container { max-width: 1200px; margin: auto; padding: 30px 20px; }

        .success { background: #c8e6c9; color: #2e7d32; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .actions-bar h2 { margin: 0; color: #2e7d32; font-size: 18px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-family: inherit; font-size: 13px; cursor: pointer; border: none; display: inline-block; }
        .btn-primary { background: #2e7d32; color: white; }
        .btn-primary:hover { background: #1b5e20; }
        .btn-secondary { background: #cfd8dc; color: #37474f; }
        .btn-danger { background: #e53935; color: white; }
        .btn-warning { background: #f9a825; color: white; }
        .btn-sm { padding: 7px 14px; font-size: 12px; }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 18px;
        }
        .gallery-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 18px rgba(0,0,0,0.07);
            position: relative;
        }
        .gallery-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
        }
        .gallery-caption {
            padding: 12px 14px;
            font-size: 13px;
            color: #546e7a;
        }
        .gallery-actions {
            padding: 10px 14px;
            border-top: 1px solid #eceff1;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .badge-cover {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #2e7d32;
            color: white;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 12px;
        }

        .empty {
            background: white;
            border-radius: 12px;
            padding: 60px 20px;
            text-align: center;
            color: #90a4ae;
            box-shadow: 0 5px 18px rgba(0,0,0,0.06);
        }
        .empty .icon { font-size: 60px; opacity: .4; margin-bottom: 20px; }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>
<body>

<div class="header">
    <h1>🖼 گالری تصاویر: {{ $village->name }}</h1>
    <div class="nav">
        <a href="{{ route('admin.villages.edit', $village->id) }}">ویرایش روستا</a>
        <a href="{{ route('admin.dashboard') }}">داشبورد</a>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="actions-bar">
        <h2>{{ $images->count() }} تصویر</h2>
        <a href="{{ route('admin.village.images.create', $village->id) }}" class="btn btn-primary">➕ افزودن تصاویر</a>
    </div>

    @if($images->count() > 0)
        <div class="gallery-grid">
            @foreach($images as $image)
                <div class="gallery-item">
                    @if($village->cover_image === $image->image)
                        <span class="badge-cover">⭐ کاور</span>
                    @endif

                    <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->title }}">

                    @if($image->title)
                        <div class="gallery-caption">{{ $image->title }}</div>
                    @endif

                    <div class="gallery-actions">
                        @if($village->cover_image !== $image->image)
                            <form method="POST" action="{{ route('admin.village.images.setCover', [$village->id, $image->id]) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">⭐ کاور</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.village.images.destroy', [$village->id, $image->id]) }}" style="display:inline;" onsubmit="return confirm('حذف این تصویر؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">🗑 حذف</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty">
            <div class="icon">📷</div>
            <p>هنوز تصویری برای این روستا آپلود نشده است.</p>
            <a href="{{ route('admin.village.images.create', $village->id) }}" class="btn btn-primary" style="margin-top:16px;">➕ آپلود اولین تصاویر</a>
        </div>
    @endif

</div>

</body>
</html>
