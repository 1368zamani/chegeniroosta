@extends('admin.village.layout')

@section('title', 'درخواست‌های مردمی')

@section('content')

<div class="page-title">
    <h2>📩 درخواست‌های مردمی روستای {{ $village->name }}</h2>
</div>

@if(session('success'))
    <div style="background:#fdeee7;color:#8e2f18;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
        {{ session('success') }}
    </div>
@endif

{{-- آمار --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px;">
    <div style="background:white;padding:16px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.05);text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#c94a2b;">{{ $stats['total'] }}</div>
        <div style="font-size:12px;color:#5a4a3c;margin-top:4px;">کل درخواست‌ها</div>
    </div>
    <div style="background:white;padding:16px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.05);text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#f5b841;">{{ $stats['pending'] }}</div>
        <div style="font-size:12px;color:#5a4a3c;margin-top:4px;">در انتظار بررسی</div>
    </div>
    <div style="background:white;padding:16px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.05);text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#c94a2b;">{{ $stats['resolved'] }}</div>
        <div style="font-size:12px;color:#5a4a3c;margin-top:4px;">پاسخ داده شده</div>
    </div>
    <div style="background:white;padding:16px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.05);text-align:center;">
        <div style="font-size:24px;font-weight:800;color:#b3261e;">{{ $stats['rejected'] }}</div>
        <div style="font-size:12px;color:#5a4a3c;margin-top:4px;">رد شده</div>
    </div>
</div>

{{-- فیلتر --}}
<div class="card" style="margin-bottom:16px;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
        <div style="flex:1;min-width:200px;">
            <label style="display:block;font-size:13px;margin-bottom:6px;color:#5a4a3c;">جستجو</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="کد پیگیری، نام، تلفن، موضوع..." style="width:100%;padding:10px;border:1px solid #e6d8c3;border-radius:8px;font-family:inherit;">
        </div>
        <div style="min-width:160px;">
            <label style="display:block;font-size:13px;margin-bottom:6px;color:#5a4a3c;">وضعیت</label>
            <select name="status" style="width:100%;padding:10px;border:1px solid #e6d8c3;border-radius:8px;font-family:inherit;">
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
            <button type="submit" style="padding:10px 20px;background:#c94a2b;color:white;border:none;border-radius:8px;cursor:pointer;font-family:inherit;">فیلتر</button>
            @if(request()->filled('status') || request()->filled('q'))
                <a href="{{ route('admin.village.requests.index', $village->id) }}" style="padding:10px 16px;background:#f5efe4;color:#c94a2b;border-radius:8px;text-decoration:none;margin-right:6px;">پاک کردن</a>
            @endif
        </div>
    </form>
</div>

{{-- لیست --}}
@if($requests->count() > 0)
    <div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 3px 12px rgba(0,0,0,.06);">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#fffdf9;">
                    <th style="padding:12px;text-align:right;font-size:13px;color:#5a4a3c;">کد</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#5a4a3c;">نام</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#5a4a3c;">موضوع</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#5a4a3c;">وضعیت</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#5a4a3c;">تاریخ</th>
                    <th style="padding:12px;text-align:right;font-size:13px;color:#5a4a3c;">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                    <tr style="border-top:1px solid #f5efe4;">
                        <td style="padding:12px;font-size:12.5px;font-family:monospace;direction:ltr;color:#c94a2b;font-weight:700;">{{ $req->tracking_code }}</td>
                        <td style="padding:12px;font-size:13px;">{{ $req->full_name }}</td>
                        <td style="padding:12px;font-size:13px;">{{ $req->subject }}</td>
                        <td style="padding:12px;">
                            @php
                                $badges = [
                                    'submitted'  => ['bg'=>'#e6d8c3','color'=>'#5a4a3c','label'=>'ثبت شده'],
                                    'reviewing'  => ['bg'=>'#fdeec9','color'=>'#a8730f','label'=>'در حال بررسی'],
                                    'assigned'   => ['bg'=>'#fdeec9','color'=>'#a8730f','label'=>'ارجاع شده'],
                                    'processing' => ['bg'=>'#fdeec9','color'=>'#a8730f','label'=>'در حال پیگیری'],
                                    'resolved'   => ['bg'=>'#fdeee7','color'=>'#8e2f18','label'=>'پاسخ داده شده'],
                                    'rejected'   => ['bg'=>'#fdeceb','color'=>'#991b1b','label'=>'رد شده'],
                                ];
                                $b = $badges[$req->status] ?? ['bg'=>'#e6d8c3','color'=>'#5a4a3c','label'=>$req->status];
                            @endphp
                            <span style="display:inline-block;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;background:{{ $b['bg'] }};color:{{ $b['color'] }};">
                                {{ $b['label'] }}
                            </span>
                        </td>
                        <td style="padding:12px;font-size:12px;color:#5a4a3c;">{{ jalali($req->created_at, 'Y/m/d H:i') }}</td>
                        <td style="padding:12px;">
                            <a href="{{ route('admin.village.requests.show', [$village->id, $req->id]) }}"
                               style="display:inline-block;padding:6px 14px;background:#c94a2b;color:white;text-decoration:none;border-radius:6px;font-size:12px;">
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
    <div style="background:white;padding:60px 20px;text-align:center;border-radius:12px;color:#8a7a6a;">
        <div style="font-size:50px;margin-bottom:12px;">📭</div>
        <p>هیچ درخواستی یافت نشد.</p>
    </div>
@endif

@endsection
