@extends('admin.village.layout')

@section('title', $product->exists ? 'ویرایش محصول' : 'محصول جدید')

@section('content')

@php
    $isEdit = $product->exists;
    $fieldStyle = 'width:100%; padding:11px 14px; border:1.5px solid #d7dee3; border-radius:8px; font-family:inherit; font-size:14px;';
    $labelStyle = 'display:block; font-size:13px; font-weight:bold; color:#37474f; margin-bottom:6px;';
    $cardStyle  = 'background:#fff; border-radius:12px; padding:22px; box-shadow:0 4px 16px rgba(0,0,0,.05); margin-bottom:16px;';
    $hintStyle  = 'font-size:11.5px; color:#90a4ae; margin-top:5px;';
@endphp

<div class="page-title">
    <h2>{{ $isEdit ? '✏️ ویرایش محصول' : '➕ محصول جدید' }} — {{ $village->name }}</h2>
    <a href="{{ route('admin.village.products.index', $village->id) }}" class="btn"
       style="background:#eceff1; color:#37474f;">← بازگشت</a>
</div>

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
@endif

<form method="POST"
      action="{{ $isEdit
            ? route('admin.village.products.update', [$village->id, $product->id])
            : route('admin.village.products.store', $village->id) }}"
      enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- ═══ ۱) اطلاعات اصلی ═══ --}}
    <div style="{{ $cardStyle }}">
        <div style="font-size:14px; font-weight:800; color:#2e7d32; margin-bottom:16px;">
            ۱) اطلاعات اصلی محصول
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:16px;">
            <div>
                <label style="{{ $labelStyle }}">نام محصول *</label>
                <input type="text" name="title" required maxlength="200"
                       value="{{ old('title', $product->title) }}"
                       placeholder="مثلاً: عسل طبیعی کوهی"
                       style="{{ $fieldStyle }}">
            </div>

            <div>
                <label style="{{ $labelStyle }}">دسته‌بندی</label>
                <select name="category" style="{{ $fieldStyle }}">
                    <option value="">— انتخاب کنید —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected(old('category', $product->category) === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-top:16px;">
            <label style="{{ $labelStyle }}">توضیح کوتاه</label>
            <input type="text" name="short_description" maxlength="300"
                   value="{{ old('short_description', $product->short_description) }}"
                   placeholder="یک جمله کوتاه برای نمایش در کارت محصول"
                   style="{{ $fieldStyle }}">
            <div style="{{ $hintStyle }}">حداکثر ۳۰۰ کاراکتر — در فهرست محصولات دیده می‌شود.</div>
        </div>

        <div style="margin-top:16px;">
            <label style="{{ $labelStyle }}">مشخصات کامل</label>
            <textarea name="description" rows="6" maxlength="5000"
                      placeholder="مشخصات محصول، مواد اولیه، وزن، بسته‌بندی، شرایط نگهداری و…"
                      style="{{ $fieldStyle }} resize:vertical;">{{ old('description', $product->description) }}</textarea>
            <div style="{{ $hintStyle }}">حداکثر ۵۰۰۰ کاراکتر</div>
        </div>
    </div>

    {{-- ═══ ۲) قیمت و موجودی ═══ --}}
    <div style="{{ $cardStyle }}">
        <div style="font-size:14px; font-weight:800; color:#2e7d32; margin-bottom:16px;">
            ۲) قیمت و موجودی
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
            <div>
                <label style="{{ $labelStyle }}">قیمت (تومان)</label>
                <input type="number" name="price" min="0" max="999999999999" step="1"
                       value="{{ old('price', $product->price) }}"
                       placeholder="مثلاً: 350000" dir="ltr"
                       style="{{ $fieldStyle }}">
                <div style="{{ $hintStyle }}">خالی بگذارید → «تماس بگیرید» نمایش داده می‌شود.</div>
            </div>

            <div>
                <label style="{{ $labelStyle }}">واحد</label>
                <input type="text" name="unit" maxlength="40"
                       value="{{ old('unit', $product->unit) }}"
                       placeholder="کیلوگرم / عدد / بسته / لیتر"
                       style="{{ $fieldStyle }}">
            </div>

            <div>
                <label style="{{ $labelStyle }}">موجودی</label>
                <input type="text" name="quantity" maxlength="60"
                       value="{{ old('quantity', $product->quantity) }}"
                       placeholder="مثلاً: ۱۰۰ کیلوگرم"
                       style="{{ $fieldStyle }}">
            </div>
        </div>
    </div>

    {{-- ═══ ۳) فروشنده و تحویل ═══ --}}
    <div style="{{ $cardStyle }}">
        <div style="font-size:14px; font-weight:800; color:#2e7d32; margin-bottom:16px;">
            ۳) فروشنده و تحویل
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
            <div>
                <label style="{{ $labelStyle }}">نام فروشنده</label>
                <input type="text" name="seller_name" maxlength="120"
                       value="{{ old('seller_name', $product->seller_name) }}"
                       placeholder="نام شخص یا تعاونی"
                       style="{{ $fieldStyle }}">
            </div>

            <div>
                <label style="{{ $labelStyle }}">شماره تماس</label>
                <input type="text" name="seller_phone" maxlength="30"
                       value="{{ old('seller_phone', $product->seller_phone) }}"
                       placeholder="۰۹۱۲…" dir="ltr"
                       style="{{ $fieldStyle }}">
            </div>
        </div>

        <div style="margin-top:16px;">
            <label style="{{ $labelStyle }}">محل / روش تحویل</label>
            <input type="text" name="delivery_info" maxlength="300"
                   value="{{ old('delivery_info', $product->delivery_info) }}"
                   placeholder="مثلاً: تحویل حضوری در دهیاری روستا"
                   style="{{ $fieldStyle }}">
        </div>
    </div>

    {{-- ═══ ۴) تصاویر ═══ --}}
    <div style="{{ $cardStyle }}">
        <div style="font-size:14px; font-weight:800; color:#2e7d32; margin-bottom:16px;">
            ۴) تصاویر محصول
        </div>

        <input type="file" name="images[]" accept="image/*" multiple
               style="{{ $fieldStyle }} padding:10px; background:#fafbfc;">
        <div style="{{ $hintStyle }}">
            می‌توانید چند تصویر انتخاب کنید (حداکثر ۸ تصویر — هر کدام حداکثر
            {{ \App\Support\Number::fa(number_format($maxImageKb / 1024)) }} مگابایت).
            اولین تصویر به‌عنوان تصویر اصلی نمایش داده می‌شود.
        </div>

        @if($isEdit && $product->images->count() > 0)
            <div style="margin-top:16px;">
                <div style="font-size:12.5px; color:#546e7a; margin-bottom:10px;">
                    تصاویر فعلی ({{ \App\Support\Number::fa($product->images->count()) }})
                </div>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(110px, 1fr)); gap:10px;">
                    @foreach($product->images as $img)
                        <div style="position:relative; border-radius:9px; overflow:hidden; border:1px solid #e0e6ea;">
                            <img src="{{ asset('storage/' . $img->image) }}" alt=""
                                 style="width:100%; height:90px; object-fit:cover; display:block;">
                            <form method="POST"
                                  action="{{ route('admin.village.products.image.destroy', [$village->id, $product->id, $img->id]) }}"
                                  style="position:absolute; top:4px; left:4px;"
                                  onsubmit="return confirm('این تصویر حذف شود؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="background:rgba(229,57,53,.92); color:#fff; border:none;
                                               width:24px; height:24px; border-radius:50%; cursor:pointer;
                                               font-size:13px; line-height:1;">×</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ═══ ۵) فایل‌های ضمیمه ═══ --}}
    <div style="{{ $cardStyle }}">
        <div style="font-size:14px; font-weight:800; color:#2e7d32; margin-bottom:16px;">
            ۵) فایل‌های ضمیمه (آپلود / دانلود)
        </div>

        <div style="background:#f1f8e9; border-radius:8px; padding:12px 14px; margin-bottom:14px;
                    font-size:12.5px; color:#33691e; line-height:1.9;">
            می‌توانید برای هر محصول فایل‌هایی مثل <strong>کاتالوگ، لیست قیمت، مشخصات فنی، مجوز بهداشت</strong> بارگذاری کنید.
            شهروندان می‌توانند آن‌ها را از صفحه‌ی محصول دانلود کنند.
        </div>

        <div style="display:grid; gap:10px;">
            @for($i = 0; $i < 3; $i++)
                <div style="display:grid; grid-template-columns:1fr 1.4fr; gap:10px; align-items:center;">
                    <input type="text" name="file_titles[]" maxlength="200"
                           placeholder="عنوان فایل (مثلاً: کاتالوگ محصول)"
                           style="{{ $fieldStyle }} font-size:13px; padding:9px 12px;">
                    <input type="file" name="files[]"
                           style="{{ $fieldStyle }} font-size:12.5px; padding:8px; background:#fafbfc;">
                </div>
            @endfor
        </div>
        <div style="{{ $hintStyle }}">
            حداکثر ۳ فایل در هر بار (هر فایل تا
            {{ \App\Support\Number::fa(number_format($maxFileKb / 1024)) }} مگابایت) — برای فایل بیشتر، بعد از ذخیره دوباره ویرایش کنید.
        </div>

        @if($isEdit && $product->files->count() > 0)
            <div style="margin-top:16px; border-top:1px solid #eceff1; padding-top:14px;">
                <div style="font-size:12.5px; color:#546e7a; margin-bottom:10px;">
                    فایل‌های فعلی ({{ \App\Support\Number::fa($product->files->count()) }})
                </div>
                @foreach($product->files as $f)
                    <div style="display:flex; align-items:center; gap:10px; background:#f8fafc;
                                border-radius:8px; padding:10px 12px; margin-bottom:8px; flex-wrap:wrap;">
                        <span style="font-size:17px;">{{ $f->icon() }}</span>
                        <span style="flex:1; min-width:120px; font-size:12.5px; color:#37474f;">
                            {{ $f->title }}
                            <span style="color:#90a4ae; font-size:11.5px;">({{ $f->sizeLabel() }})</span>
                        </span>
                        <span style="font-size:11.5px; color:#90a4ae;">
                            ⬇️ {{ \App\Support\Number::fa(number_format($f->download_count)) }}
                        </span>
                        <form method="POST"
                              action="{{ route('admin.village.products.file.destroy', [$village->id, $product->id, $f->id]) }}"
                              style="margin:0;"
                              onsubmit="return confirm('فایل «{{ $f->title }}» حذف شود؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="background:#e53935; color:#fff; border:none; border-radius:6px;
                                           padding:5px 12px; font-size:11.5px; cursor:pointer; font-family:inherit;">
                                🗑 حذف
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ═══ ۶) وضعیت و ترتیب ═══ --}}
    <div style="{{ $cardStyle }}">
        <div style="font-size:14px; font-weight:800; color:#2e7d32; margin-bottom:16px;">
            ۶) وضعیت نمایش
        </div>

        <div style="display:flex; gap:24px; flex-wrap:wrap; margin-bottom:16px;">
            <label style="display:flex; align-items:center; gap:9px; cursor:pointer; font-size:13.5px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $product->is_active ?? true))
                       style="width:20px; height:20px;">
                <span style="font-weight:bold; color:#37474f;">فعال (نمایش در سایت)</span>
            </label>

            <label style="display:flex; align-items:center; gap:9px; cursor:pointer; font-size:13.5px;">
                <input type="hidden" name="is_available" value="0">
                <input type="checkbox" name="is_available" value="1"
                       @checked(old('is_available', $product->is_available ?? true))
                       style="width:20px; height:20px;">
                <span style="font-weight:bold; color:#37474f;">موجود برای فروش</span>
            </label>

            <label style="display:flex; align-items:center; gap:9px; cursor:pointer; font-size:13.5px;">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1"
                       @checked(old('is_featured', $product->is_featured ?? false))
                       style="width:20px; height:20px;">
                <span style="font-weight:bold; color:#37474f;">⭐ محصول ویژه (بالای فهرست)</span>
            </label>
        </div>

        <div style="max-width:220px;">
            <label style="{{ $labelStyle }}">ترتیب نمایش</label>
            <input type="number" name="sort_order" min="0" max="9999"
                   value="{{ old('sort_order', $product->sort_order ?? 0) }}" dir="ltr"
                   style="{{ $fieldStyle }}">
            <div style="{{ $hintStyle }}">عدد کوچکتر = بالاتر</div>
        </div>
    </div>

    <div style="display:flex; gap:10px; justify-content:flex-end; margin-bottom:40px;">
        <a href="{{ route('admin.village.products.index', $village->id) }}"
           class="btn" style="background:#eceff1; color:#37474f;">انصراف</a>
        <button type="submit" class="btn btn-primary" style="padding:12px 28px;">
            {{ $isEdit ? '💾 ذخیره‌ی تغییرات' : '➕ ثبت محصول' }}
        </button>
    </div>
</form>

@endsection
