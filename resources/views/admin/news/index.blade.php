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
            background: #f5f7fa;
            color: #263238;
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
            color: #1565c0;
        }

        h3 {
            margin-top: 0;
        }

        .meta {
            color: #78909c;
            line-height: 2;
        }

        .btn {
            display: inline-block;
            background: #1565c0;
            color: white;
            text-decoration: none;
            padding: 10px 17px;
            border-radius: 7px;
            border: 0;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-danger {
            background: #c62828;
        }

        .btn-gray {
            background: #607d8b;
        }

        .message {
            background: #e8f5e9;
            color: #2e7d32;
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
