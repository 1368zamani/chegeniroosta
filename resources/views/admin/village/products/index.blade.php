@extends('admin.village.layout')

@section('title', 'محصولات روستا')

@section('content')

<div class="page-title">
    <h2>🛒 محصولات {{ $village->name }}</h2>
    <a href="{{ route('admin.village.products.create', $village->id) }}" class="btn btn-primary">
        ➕ محصول جدید
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
@endif

<div class="card" style="background:#f1f8e9; border-right:4px solid #2e7d32; margin-bottom:18px;">
    <p style="margin:0; font-size:13px; color:#33691e; line-height:1.9;">
        محصولات این بخش در صفحه‌ی <strong>«محصولات روستا»</strong> در سایت عمومی نمایش داده می‌شوند
        و شهروندان می‌توانند مشخصات، تصاویر و فایل‌های ضمیمه‌ی هر محصول را ببینند.
        <br>
        برای هر محصول می‌توانید چند تصویر و چند فایل (کاتالوگ، مشخصات، مجوز) بارگذاری کنید.
    </p>
</div>

@if($products->count() > 0)

<table>
    <thead>
        <tr>
            <th style="width:70px;">تصویر</th>
            <th>نام محصول</th>
            <th style="width:110px;">دسته</th>
            <th style="width:130px;">قیمت</th>
            <th style="width:90px;">موجودی</th>
            <th style="width:70px;">فایل</th>
            <th style="width:80px;">وضعیت</th>
            <th style="width:180px;">عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $p)
            @php $img = $p->images->first(); @endphp
            <tr>
                <td>
                    @if($img)
                        <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $p->title }}"
                             style="width:56px; height:56px; object-fit:cover; border-radius:8px; display:block;">
                    @else
                        <div style="width:56px; height:56px; border-radius:8px; background:#e8f5e9;
                                    display:flex; align-items:center; justify-content:center; font-size:22px;">
                            {{ $p->categoryIcon() }}
                        </div>
                    @endif
                </td>
                <td>
                    <strong style="font-size:14px;">{{ $p->title }}</strong>
                    @if($p->is_featured)
                        <span class="badge" style="background:#fff3e0; color:#ef6c00; font-size:10.5px;">⭐ ویژه</span>
                    @endif
                    @if($p->short_description)
                        <div style="font-size:11.5px; color:#78909c; margin-top:3px;">
                            {{ \Illuminate\Support\Str::limit($p->short_description, 60) }}
                        </div>
                    @endif
                </td>
                <td style="font-size:12px; color:#546e7a;">
                    {{ $p->category ?: '—' }}
                </td>
                <td style="font-size:12.5px; font-weight:600; color:#2e7d32;">
                    {{ $p->priceLabel() }}
                </td>
                <td style="font-size:12px; color:#607d8b;">
                    {{ $p->quantity ?: '—' }}
                </td>
                <td style="font-size:12px; text-align:center;">
                    @if($p->files->count() > 0)
                        📎 {{ \App\Support\Number::fa($p->files->count()) }}
                    @else
                        —
                    @endif
                </td>
                <td>
                    @if($p->is_active)
                        <span class="badge" style="background:#c8e6c9;color:#2e7d32;">فعال</span>
                    @else
                        <span class="badge" style="background:#ffcdd2;color:#c62828;">غیرفعال</span>
                    @endif
                </td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('admin.village.products.edit', [$village->id, $p->id]) }}"
                       class="btn btn-primary" style="font-size:12px; padding:6px 11px;">✏️ ویرایش</a>

                    <form method="POST"
                          action="{{ route('admin.village.products.toggle', [$village->id, $p->id]) }}"
                          style="display:inline;">
                        @csrf
                        <button type="submit" class="btn"
                                style="font-size:12px; padding:6px 11px; background:{{ $p->is_active ? '#f9a825' : '#43a047' }}; color:#fff;"
                                title="{{ $p->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}">
                            {{ $p->is_active ? '⏸' : '▶' }}
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('admin.village.products.destroy', [$village->id, $p->id]) }}"
                          style="display:inline;"
                          onsubmit="return confirm('محصول «{{ $p->title }}» حذف شود؟\n\nتصاویر و فایل‌های آن هم پاک می‌شوند.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn"
                                style="background:#e53935; color:#fff; font-size:12px; padding:6px 11px;">🗑</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:16px;">
    {{ $products->links() }}
</div>

@else
    <div style="background:#fff; border-radius:12px; padding:60px 20px; text-align:center;
                box-shadow:0 4px 16px rgba(0,0,0,.05);">
        <div style="font-size:44px; margin-bottom:12px; opacity:.5;">🛒</div>
        <p style="color:#90a4ae; margin:0 0 16px; font-size:14px;">
            هنوز محصولی برای این روستا ثبت نشده است.
        </p>
        <a href="{{ route('admin.village.products.create', $village->id) }}"
           class="btn btn-primary">➕ اولین محصول را اضافه کنید</a>
    </div>
@endif

@endsection
