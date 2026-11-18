@extends('admin.village.layout')

@section('title', 'درخواست ' . $request->tracking_code)

@section('content')

<div class="page-title">
    <h2>📩 جزئیات درخواست</h2>
    <a href="{{ route('admin.village.requests.index', $village->id) }}" style="display:inline-block;padding:8px 16px;background:#f5efe4;color:#c94a2b;text-decoration:none;border-radius:8px;font-size:13px;">
        → بازگشت به لیست
    </a>
</div>

@if(session('success'))
    <div style="background:#fdeee7;color:#8e2f18;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <h3 style="margin-top:0;">📋 اطلاعات درخواست</h3>

    <div style="display:grid;grid-template-columns:140px 1fr;gap:8px 16px;padding:12px 0;border-bottom:1px solid #f5efe4;">
        <strong style="color:#5a4a3c;font-size:13px;">کد پیگیری:</strong>
        <span style="font-family:monospace;direction:ltr;color:#c94a2b;font-weight:700;font-size:15px;">{{ $request->tracking_code }}</span>

        <strong style="color:#5a4a3c;font-size:13px;">نام درخواست‌کننده:</strong>
        <span>{{ $request->full_name }}</span>

        <strong style="color:#5a4a3c;font-size:13px;">شماره تماس:</strong>
        <span style="direction:ltr;display:inline-block;">
            <a href="tel:{{ $request->phone }}" style="color:#c94a2b;text-decoration:none;">{{ $request->phone }}</a>
        </span>

        <strong style="color:#5a4a3c;font-size:13px;">موضوع:</strong>
        <span>{{ $request->subject }}</span>

        <strong style="color:#5a4a3c;font-size:13px;">تاریخ ثبت:</strong>
        <span>{{ jalali($request->created_at, 'Y/m/d H:i') }}</span>

        <strong style="color:#5a4a3c;font-size:13px;">IP:</strong>
        <span style="font-family:monospace;font-size:12px;color:#8a7a6a;">{{ $request->ip_address ?? '—' }}</span>
    </div>

    <div style="padding:16px 0;">
        <strong style="color:#5a4a3c;font-size:13px;display:block;margin-bottom:8px;">متن درخواست:</strong>
        <div style="background:#fffdf9;padding:14px;border-radius:8px;line-height:2;font-size:14px;white-space:pre-line;">{{ $request->description }}</div>
    </div>

    @if($request->attachment)
        <div style="padding:12px 0;">
            <strong style="color:#5a4a3c;font-size:13px;display:block;margin-bottom:8px;">فایل پیوست:</strong>
            <a href="{{ asset('storage/' . $request->attachment) }}" target="_blank" style="display:inline-block;padding:8px 16px;background:#fff7dc;color:#c94a2b;text-decoration:none;border-radius:8px;border:1px solid #f0a58c;">
                📎 دانلود فایل
            </a>
        </div>
    @endif
</div>

<form method="POST" action="{{ route('admin.village.requests.update', [$village->id, $request->id]) }}">
    @csrf
    @method('PUT')

    <div class="card">
        <h3 style="margin-top:0;">✍️ پاسخ و تغییر وضعیت</h3>

        <div class="field">
            <label>وضعیت درخواست</label>
            <select name="status" required style="width:100%;padding:10px;border:1px solid #e6d8c3;border-radius:8px;font-family:inherit;">
                <option value="submitted"  @selected($request->status=='submitted')>ثبت شده</option>
                <option value="reviewing"  @selected($request->status=='reviewing')>در حال بررسی</option>
                <option value="assigned"   @selected($request->status=='assigned')>ارجاع داده شده</option>
                <option value="processing" @selected($request->status=='processing')>در حال پیگیری</option>
                <option value="resolved"   @selected($request->status=='resolved')>پاسخ داده شده</option>
                <option value="rejected"   @selected($request->status=='rejected')>رد شده</option>
            </select>
        </div>

        <div class="field">
            <label>پاسخ (اختیاری — برای کاربر نمایش داده می‌شود)</label>
            <textarea name="admin_response" style="min-height:140px;width:100%;padding:10px;border:1px solid #e6d8c3;border-radius:8px;font-family:inherit;resize:vertical;" placeholder="پاسخ خود را بنویسید...">{{ old('admin_response', $request->admin_response) }}</textarea>
            <div style="font-size:12px;color:#5a4a3c;margin-top:6px;">این متن در صفحه پیگیری برای کاربر نمایش داده می‌شود.</div>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="padding:12px 24px;background:#c94a2b;color:white;border:none;border-radius:8px;cursor:pointer;font-family:inherit;font-weight:600;">
                💾 ذخیره پاسخ
            </button>
            <a href="{{ route('admin.village.requests.index', $village->id) }}" style="padding:12px 20px;background:#f5efe4;color:#c94a2b;text-decoration:none;border-radius:8px;">انصراف</a>
        </div>
    </div>
</form>

@endsection
