<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ویرایش {{ $village->name }}</title>

    <style>
        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f7fa;
            color: #263238;
        }

        .container {
            width: min(900px, 94%);
            margin: 30px auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,.07);
        }

        h1 {
            color: #1565c0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #cfd8dc;
            border-radius: 7px;
            font-family: inherit;
            font-size: 14px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .actions {
            margin-top: 25px;
        }

        button,
        .back {
            border: 0;
            background: #1565c0;
            color: white;
            padding: 11px 22px;
            border-radius: 7px;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
            font-size: 14px;
        }

        .back {
            background: #607d8b;
            margin-right: 8px;
        }

        @media(max-width:700px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <h1>ویرایش روستا: {{ $village->name }}</h1>

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.villages.update', $village) }}">

            @csrf
            @method('PUT')

            <div class="grid">

                <div>
                    <label>نام روستا</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $village->name) }}"
                           required>
                </div>

                <div>
                    <label>Slug</label>
                    <input type="text"
                           name="slug"
                           value="{{ old('slug', $village->slug) }}"
                           required>
                </div>

                <div>
                    <label>Subdomain</label>
                    <input type="text"
                           name="subdomain"
                           value="{{ old('subdomain', $village->subdomain) }}"
                           placeholder="مثلاً sabzevar">
                </div>

                <div>
                    <label>استان</label>
                    <input type="text"
                           name="province"
                           value="{{ old('province', $village->province) }}">
                </div>

                <div>
                    <label>شهرستان</label>
                    <input type="text"
                           name="county"
                           value="{{ old('county', $village->county) }}">
                </div>

                <div>
                    <label>بخش</label>
                    <input type="text"
                           name="section"
                           value="{{ old('section', $village->section) }}">
                </div>

                <div>
                    <label>دهستان</label>
                    <input type="text"
                           name="district"
                           value="{{ old('district', $village->district) }}">
                </div>

                <div>
                    <label>نام دهیار</label>
                    <input type="text"
                           name="dehyar_name"
                           value="{{ old('dehyar_name', $village->dehyar_name) }}">
                </div>

                <div class="field">
                    <label>لینک اینستاگرام دهیار</label>
                    <input type="text" name="dehyar_instagram" value="{{ old('dehyar_instagram', $village->dehyar_instagram ?? '') }}" dir="ltr" placeholder="مثال: dehyar_abuzar">
                    <div class="hint">بدون @ — نام کاربری یا آدرس کامل</div>
                </div>

                <div>
                    <label>رئیس شورا</label>
                    <input type="text"
                           name="council_head_name"
                           value="{{ old('council_head_name', $village->council_head_name) }}">
                </div>

                <div>
                    <label>جمعیت</label>
                    <input type="number"
                           name="population"
                           value="{{ old('population', $village->population) }}"
                           min="0">
                </div>

                <div>
                    <label>تعداد خانوار</label>
                    <input type="number"
                           name="households"
                           value="{{ old('households', $village->households) }}"
                           min="0">
                </div>

                <div>
                    <label>مساحت</label>
                    <input type="number"
                           step="0.01"
                           name="area"
                           value="{{ old('area', $village->area) }}"
                           min="0">
                </div>

                <div>
                    <label>تلفن</label>
                    <input type="text"
                           name="phone"
                           value="{{ old('phone', $village->phone) }}">
                </div>

                <div>
                    <label>ایمیل</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $village->email) }}">
                </div>

                <div class="full">
                    <label>آدرس</label>
                    <textarea name="address">{{ old('address', $village->address) }}</textarea>
                </div>

                <div class="full">
                    <label>توضیحات</label>
                    <textarea name="description">{{ old('description', $village->description) }}</textarea>
                </div>

                <div class="full">
                    <label>
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               style="width:auto"
                               {{ old('is_active', $village->is_active) ? 'checked' : '' }}>
                        روستا فعال باشد
                    </label>
                </div>

            </div>

            <div class="actions">

                <button type="submit">
                    ذخیره تغییرات
                </button>

                <a class="back"
                   href="{{ route('admin.villages.index') }}">
                    بازگشت
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>
