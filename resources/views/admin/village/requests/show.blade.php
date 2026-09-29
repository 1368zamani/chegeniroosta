@extends('admin.village.layout')

@section('title', 'درخواست ' . $request->tracking_code)

@section('content')

<div class="page-title">
    <h2>📩 جزئیات درخواست</h2>
    <a href="{{ route('admin.village.requests.index', $village->id) }}" style="display:inline-block;padding:8px 16px;background:#f1f5f9;color:#1e5b9c;text-decoration:none;border-radius:8px;font-size:13px;">
        → بازگشت به لیست
    </a>
</div>

@if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <h3 style="margin-top:0;">📋 اطلاعات درخواست</h3>

    <div style="display:grid;grid-template-columns:140px 1fr;gap:8px 16px;padding:12px 0;border-bottom:1px solid #f1f5f9;">
        <strong style="color:#64748b;font-size:13px;">کد پیگیری:</strong>
        <span style="font-family:monospace;direction:ltr;color:#1e5b9c;font-weight:700;font-size:15px;">{{ $request->tracking_code }}</span>

        <strong style="color:#64748b;font-size:13px;">نام درخواست‌کننده:</strong>
        <span>{{ $request->full_name }}</span>

        <strong style="color:#64748b;font-size:13px;">شماره تماس:</strong>
        <span style="direction:ltr;display:inline-block;">
            <a href="tel:{{ $request->phone }}" style="color:#1e5b9c;text-decoration:none;">{{ $request->phone }}</a>
        </span>

        <strong style="color:#64748b;font-size:13px;">موضوع:</strong>
        <span>{{ $request->subject }}</span>

        <strong style="color:#64748b;font-size:13px;">تاریخ ثبت:</strong>
        <span>{{ jalali($request->created_at, 'Y/m/d H:i') }}</span>

        <strong style="color:#64748b;font-size:13px;">IP:</strong>
        <span style="font-family:monospace;font-size:12px;color:#94a3b8;">{{ $request->ip_address ?? '—' }}</span>
    </div>

    <div style="padding:16px 0;">
        <strong style="color:#64748b;font-size:13px;display:block;margin-bottom:8px;">متن درخواست:</strong>
        <div style="background:#f8fafc;padding:14px;border-radius:8px;line-height:2;font-size:14px;white-space:pre-line;">{{ $request->description }}</div>
    </div>

    @if($request->attachment)
        <div style="padding:12px 0;">
            <strong style="color:#64748b;font-size:13px;display:block;margin-bottom:8px;">فایل پیوست:</strong>
            <a href="{{ asset('storage/' . $request->attachment) }}" target="_blank" style="display:inline-block;padding:8px 16px;background:#f0f9ff;color:#1e5b9c;text-decoration:none;border-radius:8px;border:1px solid #bae6fd;">
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
            <select name="status" required style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-family:inherit;">
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
            <textarea name="admin_response" style="min-height:140px;width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-family:inherit;resize:vertical;" placeholder="پاسخ خود را بنویسید...">{{ old('admin_response', $request->admin_response) }}</textarea>
            <div style="font-size:12px;color:#64748b;margin-top:6px;">این متن در صفحه پیگیری برای کاربر نمایش داده می‌شود.</div>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="padding:12px 24px;background:#16a34a;color:white;border:none;border-radius:8px;cursor:pointer;font-family:inherit;font-weight:600;">
                💾 ذخیره پاسخ
            </button>
            <a href="{{ route('admin.village.requests.index', $village->id) }}" style="padding:12px 20px;background:#f1f5f9;color:#1e5b9c;text-decoration:none;border-radius:8px;">انصراف</a>
        </div>
    </div>
</form>

@endsection
