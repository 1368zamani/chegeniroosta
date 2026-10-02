<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت خبر</title>

    <style>
        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f7fa;
        }

        .container {
            width: min(850px, 94%);
            margin: 35px auto;
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 14px;
        }

        h1 {
            color: #1565c0;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 18px 0 7px;
        }

        input,
        textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #cfd8dc;
            border-radius: 7px;
            font-family: inherit;
            box-sizing: border-box;
        }

        textarea {
            min-height: 180px;
            resize: vertical;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
        }

        button,
        .back {
            margin-top: 20px;
            padding: 11px 20px;
            border: 0;
            border-radius: 7px;
            background: #1565c0;
            color: white;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        .back {
            background: #607d8b;
            display: inline-block;
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>

<body>

<div class="container">

    <div class="card">

        <h1>ثبت خبر برای {{ $village->name }}</h1>

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.village.news.store', $village) }}">

            @csrf

            <label>عنوان خبر</label>
            <input type="text"
                   name="title"
                   value="{{ old('title') }}"
                   required>

            <label>Slug</label>
            <input type="text"
                   name="slug"
                   value="{{ old('slug') }}"
                   placeholder="اختیاری">

            <label>خلاصه خبر</label>
            <textarea name="excerpt">{{ old('excerpt') }}</textarea>

            <label>متن کامل خبر</label>
            <textarea name="content" required>{{ old('content') }}</textarea>

            <label>تاریخ انتشار</label>
            <input type="datetime-local"
                   name="published_at"
                   value="{{ old('published_at') }}">

            <label>
                <input type="checkbox"
                       name="is_published"
                       value="1"
                       style="width:auto"
                       {{ old('is_published') ? 'checked' : '' }}>
                خبر منتشر شود
            </label>

            <button type="submit">
                ثبت خبر
            </button>

            <a class="back"
               href="{{ route('admin.village.news.index', $village) }}">
                بازگشت
            </a>

        </form>

    </div>

</div>

</body>
</html>
