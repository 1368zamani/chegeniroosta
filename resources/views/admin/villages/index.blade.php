<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت روستاها</title>

    <style>
        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f7fa;
            color: #263238;
        }

        .container {
            width: min(1100px, 94%);
            margin: 40px auto;
        }

        h1 {
            color: #1565c0;
        }

        .message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 12px rgba(0,0,0,.06);
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .info {
            line-height: 2;
        }

        .btn {
            display: inline-block;
            background: #1565c0;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: inherit;
        }
        .btn-toggle-off { background: #e53935; }
        .btn-toggle-off:hover { background: #c62828; }
        .btn-toggle-on { background: #43a047; }
        .btn-toggle-on:hover { background: #2e7d32; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }

        .active {
            color: #2e7d32;
            font-weight: bold;
        }

        .inactive {
            color: #c62828;
            font-weight: bold;
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>

<body>

<div class="container">

    <h1>مدیریت روستاها</h1>

    @if(session('success'))
        <div class="message">
            {{ session('success') }}
        </div>
    @endif

    @forelse($villages as $village)

        <div class="card">

            <div class="row">

                <div class="info">
                    <strong>{{ $village->name }}</strong>
                    <br>

                    Slug:
                    {{ $village->slug }}

                    <br>

                    Subdomain:
                    {{ $village->subdomain ?: 'ثبت نشده' }}

                    <br>

                    وضعیت:
                    @if($village->is_active)
                        <span class="active">فعال</span>
                    @else
                        <span class="inactive">غیرفعال</span>
                    @endif
                </div>

                <div class="actions">
                    <a class="btn"
                       href="{{ route('admin.villages.edit', $village) }}">
                        ویرایش اطلاعات
                    </a>
                    <form method="POST" action="{{ route('admin.villages.toggle', $village->id) }}" style="display:inline;">
                        @csrf
                        @if($village->is_active)
                            <button type="submit" class="btn btn-toggle-off" onclick="return confirm('روستای «{{ $village->name }}» را غیرفعال کنم؟')">🔒 غیرفعال</button>
                        @else
                            <button type="submit" class="btn btn-toggle-on">🔓 فعال</button>
                        @endif
                    </form>
                </div>

            </div>

        </div>

    @empty

        <div class="card">
            هنوز روستایی ثبت نشده است.
        </div>

    @endforelse

</div>

</body>
</html>
