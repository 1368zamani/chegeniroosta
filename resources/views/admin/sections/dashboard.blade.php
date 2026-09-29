@extends('admin.sections.layout')

@section('title', 'داشبورد')

@section('content')

<div class="page-title">
    <h2>📊 داشبورد بخشداری {{ $section->name }}</h2>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('admin.sections.edit', $section->id) }}" class="btn btn-secondary btn-sm">⚙️ تنظیمات</a>
        <a href="{{ url('/') }}" target="_blank" class="btn btn-primary btn-sm">🌐 مشاهده سایت</a>
    </div>
</div>

{{-- کارت‌های آماری --}}
<div class="stats-grid">
    <a href="{{ route('admin.sections.villages', $section->id) }}" class="stat-card">
        <span class="icon">🏘</span>
        <span class="num">{{ $stats['villages'] }}</span>
        <span class="label">روستاهای بخش</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="stat-card">
        <span class="icon">👥</span>
        <span class="num">{{ $stats['village_admins'] }}</span>
        <span class="label">ادمین روستا</span>
    </a>
    <a href="{{ route('admin.sections.all-requests', $section->id) }}" class="stat-card">
        <span class="icon">📩</span>
        <span class="num">{{ $stats['section_requests'] + $stats['village_requests'] }}</span>
        <span class="label">کل درخواست‌ها</span>
    </a>
    <a href="{{ route('admin.sections.all-requests', $section->id) }}" class="stat-card">
        <span class="icon">⏳</span>
        <span class="num">{{ $stats['pending_requests'] }}</span>
        <span class="label">در انتظار بررسی</span>
    </a>
    <a href="{{ route('admin.sections.all-requests', $section->id) }}" class="stat-card">
        <span class="icon">✅</span>
        <span class="num">{{ $stats['resolved_requests'] }}</span>
        <span class="label">پاسخ داده شده</span>
    </a>
    <a href="{{ route('admin.section-news.index') }}" class="stat-card">
        <span class="icon">📰</span>
        <span class="num">{{ $stats['news'] }}</span>
        <span class="label">خبر بخشداری</span>
    </a>
</div>

{{-- آخرین درخواست‌های بخشداری --}}
<div class="card">
    <h3>📩 آخرین درخواست‌های بخشداری</h3>
    @if($recentSectionRequests->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>کد</th>
                    <th>نام</th>
                    <th>موضوع</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSectionRequests as $req)
                    <tr>
                        <td style="font-family:monospace;direction:ltr;color:#1e5b9c;font-weight:700;">{{ $req->tracking_code }}</td>
                        <td>{{ $req->full_name }}</td>
                        <td>{{ $req->subject }}</td>
                        <td><span class="badge badge-{{ $req->status }}">{{ $req->status_label }}</span></td>
                        <td style="font-size:12px;color:#64748b;">{{ jalali($req->created_at) }}</td>
                        <td>
                            <a href="{{ route('admin.sections.requests.show', [$section->id, $req->id]) }}" class="btn btn-primary btn-sm">مشاهده</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="text-align:center;margin-top:14px;">
            <a href="{{ route('admin.sections.requests.index', $section->id) }}" class="btn btn-secondary btn-sm">مشاهده همه →</a>
        </div>
    @else
        <div class="empty-box">هیچ درخواستی برای بخشداری ثبت نشده است.</div>
    @endif
</div>

{{-- آخرین درخواست‌های روستاها --}}
<div class="card">
    <h3>📬 آخرین درخواست‌های روستاهای بخش</h3>
    @if($recentVillageRequests->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>کد</th>
                    <th>روستا</th>
                    <th>نام</th>
                    <th>موضوع</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentVillageRequests as $req)
                    <tr>
                        <td style="font-family:monospace;direction:ltr;color:#1e5b9c;font-weight:700;">{{ $req->tracking_code }}</td>
                        <td>{{ optional($req->village)->name ?? '—' }}</td>
                        <td>{{ $req->full_name }}</td>
                        <td>{{ $req->subject }}</td>
                        <td><span class="badge badge-{{ $req->status }}">{{ $req->status_label }}</span></td>
                        <td style="font-size:12px;color:#64748b;">{{ jalali($req->created_at) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="text-align:center;margin-top:14px;">
            <a href="{{ route('admin.sections.all-requests', $section->id) }}" class="btn btn-secondary btn-sm">مشاهده همه →</a>
        </div>
    @else
        <div class="empty-box">هیچ درخواستی از روستاها ثبت نشده است.</div>
    @endif
</div>

{{-- روستاهای بخش (پیش‌نمایش) --}}
<div class="card">
    <h3>🏘 روستاهای بخش</h3>
    @if($villages->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>نام</th>
                    <th>دهیار</th>
                    <th>خبر</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($villages as $v)
                    <tr>
                        <td><strong>{{ $v->name }}</strong></td>
                        <td>{{ $v->dehyar_name ?? '—' }}</td>
                        <td>{{ $v->news_count }}</td>
                        <td>
                            @if($v->is_active)
                                <span class="badge badge-active">فعال</span>
                            @else
                                <span class="badge badge-inactive">غیرفعال</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.village.dashboard', $v->id) }}" class="btn btn-primary btn-sm">ورود به پنل روستا</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="text-align:center;margin-top:14px;">
            <a href="{{ route('admin.sections.villages', $section->id) }}" class="btn btn-secondary btn-sm">مدیریت کامل روستاها →</a>
        </div>
    @else
        <div class="empty-box">هنوز روستایی برای این بخش ثبت نشده است.</div>
    @endif
</div>

@endsection
