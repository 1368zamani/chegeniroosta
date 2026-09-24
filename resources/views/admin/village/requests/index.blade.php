@extends('admin.village.layout')

@section('title', 'درخواست‌های مردمی')

@section('content')

<div class="page-title">
    <h2>📩 درخواست‌های مردمی روستای {{ $village->name }}</h2>
</div>

@if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
        {{ session('success') }}
    </div>
@endif

{{-- آمار --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px;">
    <div style="background:white;padding:16px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.05);text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#1e5b9c;">{{ $stats['total'] }}</div>
        <div style="font-size:12px;color:#64748b;margin-top:4px;">کل درخواست‌ها</div>
    </div>
    <div style="background:white;padding:16px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.05);text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#f59e0b;">{{ $stats['pending'] }}</div>
        <div style="font-size:12px;color:#64748b;margin-top:4px;">در انتظار بررسی</div>
    </div>
    <div style="background:white;padding:16px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.05);text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#16a34a;">{{ $stats['resolved'] }}</div>
        <div style="font-size:12px;color:#64748b;margin-top:4px;">پاسخ داده شده</div>
    </div>
    <div style="background:white;padding:16px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.05);text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#dc2626;">{{ $stats['rejected'] }}</div>
        <div style="font-size:12px;color:#64748b;margin-top:4px;">رد شده</div>
    </div>
</div>

{{-- فیلتر --}}
<div class="card" style="margin-bottom:16px;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
        <div style="flex:1;min-width:200px;">
            <label style="display:block;font-size:13px;margin-bottom:6px;color:#64748b;">جستجو</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="کد پیگیری، نام، تلفن، موضوع..." style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-family:inherit;">
        </div>
        <div style="min-width:160px;">
            <label style="display:block;font-size:13px;margin-bottom:6px;color:#64748b;">وضعیت</label>
            <select name="status" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-family:inherit;">
                <option value="">— همه —</option>
                <option value="submitted"  @selected(request('status')=='submitted')>ثبت شده</option>
                <option value="reviewing"  @selected(request('status')=='reviewing')>در حال بررسی</option>
                <option value="assigned"   @selected(request('status')=='assigned')>ارجاع داده شده</option>
                <option value="processing" @selected(request('status')=='processing')>در حال پیگیری</option>
                <option value="resolved"   @selected(request('status')=='resolved')>پاسخ داده شده</option>
                <option value="rejected"   @selected(request('status')=='rejected')>رد شده</option>
            </select>
        </div>
        <div>
            <button type="submit" style="padding:10px 20px;background:#1e5b9c;color:white;border:none;border-radius:8px;cursor:pointer;font-family:inherit;">فیلتر</button>
            @if(request()->filled('status') || request()->filled('q'))
                <a href="{{ route('admin.village.requests.index', $village->id) }}" style="padding:10px 16px;background:#f1f5f9;color:#1e5b9c;border-radius:8px;text-decoration:none;margin-right:6px;">پاک کردن</a>
            @endif
        </div>
    </form>
</div>

{{-- لیست --}}
@if($requests->count() > 0)
    <div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 3px 12px rgba(0,0,0,.06);">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:12px;text-align:right;font-size:13px;color:#546e7a;">کد</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#546e7a;">نام</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#546e7a;">موضوع</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#546e7a;">وضعیت</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#546e7a;">تاریخ</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#546e7a;">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                    <tr style="border-top:1px solid #eceff1;">
                        <td style="padding:12px;font-size:12.5px;font-family:monospace;direction:ltr;color:#1e5b9c;font-weight:700;">{{ $req->tracking_code }}</td>
                        <td style="padding:12px;font-size:13px;">{{ $req->full_name }}</td>
                        <td style="padding:12px;font-size:13px;">{{ $req->subject }}</td>
                        <td style="padding:12px;">
                            @php
                                $badges = [
                                    'submitted'  => ['bg'=>'#dbeafe','color'=>'#1e40af','label'=>'ثبت شده'],
                                    'reviewing'  => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'در حال بررسی'],
                                    'assigned'   => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'ارجاع شده'],
                                    'processing' => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'در حال پیگیری'],
                                    'resolved'   => ['bg'=>'#d1fae5','color'=>'#065f46','label'=>'پاسخ داده شده'],
                                    'rejected'   => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'رد شده'],
                                ];
                                $b = $badges[$req->status] ?? ['bg'=>'#e2e8f0','color'=>'#475569','label'=>$req->status];
                            @endphp
                            <span style="display:inline-block;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;background:{{ $b['bg'] }};color:{{ $b['color'] }};">
                                {{ $b['label'] }}
                            </span>
                        </td>
                        <td style="padding:12px;font-size:12px;color:#64748b;">{{ $req->created_at->format('Y/m/d H:i') }}</td>
                        <td style="padding:12px;">
                            <a href="{{ route('admin.village.requests.show', [$village->id, $req->id]) }}"
                               style="display:inline-block;padding:6px 14px;background:#1e5b9c;color:white;text-decoration:none;border-radius:6px;font-size:12px;">
                                مشاهده و پاسخ
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;display:flex;justify-content:center;">
        {{ $requests->links() }}
    </div>
@else
    <div style="background:white;padding:60px 20px;text-align:center;border-radius:12px;color:#94a3b8;">
        <div style="font-size:50px;margin-bottom:12px;">📭</div>
        <p>هیچ درخواستی یافت نشد.</p>
    </div>
@endif

@endsection
