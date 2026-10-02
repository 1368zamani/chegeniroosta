<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        اطلاعات تماس {{ $village->name }}
    </title>

    <style>

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f7fa;
            padding: 40px;
            margin: 0;
        }

        .box {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px #ddd;
        }

        h1 {
            color: #1565C0;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: Tahoma, Arial, sans-serif;
            font-size: 15px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #1565C0;
        }

        .error {
            color: #c62828;
            font-size: 13px;
            margin-top: 4px;
        }

        button {
            margin-top: 25px;
            background: #1565C0;
            color: white;
            border: none;
            padding: 13px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-family: Tahoma;
            font-size: 15px;
        }

        button:hover {
            opacity: 0.9;
        }

        .back {
            display: inline-block;
            margin-bottom: 15px;
            color: #1565C0;
            text-decoration: none;
        }

    </style>

<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>

<body>

    <div class="box">

        <a class="back" href="{{ route('admin.dashboard') }}">→ بازگشت به داشبورد</a>

        <h1>اطلاعات تماس روستای {{ $village->name }}</h1>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.contact.update', $village) }}">
            @csrf
            @method('PUT')

            <label for="address">آدرس</label>
            <input type="text" id="address" name="address" value="{{ old('address', $contact->address) }}">

            <label for="phone">تلفن تماس</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $contact->phone) }}">

            <label for="email">ایمیل</label>
            <input type="email" id="email" name="email" value="{{ old('email', $contact->email) }}">

            <label for="working_hours">ساعات پاسخگویی</label>
            <input type="text" id="working_hours" name="working_hours"
                   value="{{ old('working_hours', $contact->working_hours) }}"
                   placeholder="مثلا: شنبه تا چهارشنبه، ۸ الی ۱۴">

            <label for="map_embed_url">لینک نقشه (اختیاری)</label>
            <input type="text" id="map_embed_url" name="map_embed_url"
                   value="{{ old('map_embed_url', $contact->map_embed_url) }}"
                   placeholder="لینک Google Maps">

            <button type="submit">ذخیره اطلاعات تماس</button>

        </form>

    </div>

</body>

</html>
