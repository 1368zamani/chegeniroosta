<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اخبار {{ $village->name }}</title>

    <style>
        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: #fff7e9;
            color: #2b2119;
        }

        .container {
            width: min(1100px, 94%);
            margin: 35px auto;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 18px;
            box-shadow: 0 3px 12px rgba(0,0,0,.06);
        }

        h1 {
            color: #c94a2b;
        }

        h3 {
            margin-top: 0;
        }

        .meta {
            color: #8a7a6a;
            line-height: 2;
        }

        .btn {
            display: inline-block;
            background: #c94a2b;
            color: white;
            text-decoration: none;
            padding: 10px 17px;
            border-radius: 7px;
            border: 0;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-danger {
            background: #b3261e;
        }

        .btn-gray {
            background: #8a7a6a;
        }

        .message {
            background: #fdeee7;
            color: #c94a2b;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .actions {
            margin-top: 15px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        form {
            display: inline;
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"><link rel="stylesheet" href="{{ asset('css/admin-utils.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/admin-v3.css') }}?v=1">
</head>

<body>

<div class="container">

    <div class="top">
        <h1>اخبار {{ $village->name }}</h1>

        <a class="btn"
           href="{{ route('admin.village.news.create', $village) }}">
            + ثبت خبر جدید
        </a>
    </div>

    @if(session('success'))
        <div class="message">
            {{ session('success') }}
        </div>
    @endif

    @forelse($news as $item)

        <div class="card">

            <h3>{{ $item->title }}</h3>

            <div class="meta">
                وضعیت:
                {{ $item->is_published ? 'منتشر شده' : 'پیش‌نویس' }}

                @if($item->published_at)
                    <br>
                    تاریخ انتشار:
                    {{ $item->published_at }}
                @endif
            </div>

            @if($item->excerpt)
                <p>{{ $item->excerpt }}</p>
            @endif

            <div class="actions">

                <a class="btn"
                   href="{{ route('admin.village.news.edit', [$village, $item]) }}">
                    ویرایش
                </a>

                <form method="POST"
                      action="{{ route('admin.village.news.destroy', [$village, $item]) }}"
                      onsubmit="return confirm('آیا از حذف این خبر مطمئن هستید؟');">

                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger" type="submit">
                        حذف
                    </button>
                </form>

            </div>

        </div>

    @empty

        <div class="card">
            هنوز خبری برای این روستا ثبت نشده است.
        </div>

    @endforelse

</div>

</body>
</html>
