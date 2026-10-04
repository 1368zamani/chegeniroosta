@extends('admin.tariffs.layout')
@section('title', 'تعرفه‌های سالانه')

@section('content')
<div class="card">
    <h3>🔍 فیلتر و جستجو</h3>
    <form method="GET" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; align-items: end;">
        <div>
            <label>جستجو (نام روستا)</label>
            <input type="text" name="q" value="{{ request('q') }}">
        </div>
        <div>
            <label>سال</label>
            <select name="year">
                <option value="">همه</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" @selected(request('year')==$y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>وضعیت</label>
            <select name="status">
                <option value="">همه</option>
                <option value="draft" @selected(request('status')=='draft')>پیش‌نویس</option>
                <option value="proposed" @selected(request('status')=='proposed')>پیشنهادی دهیار</option>
                <option value="village_council_approved" @selected(request('status')=='village_council_approved')>تصویب روستا</option>
                <option value="district_council_approved" @selected(request('status')=='district_council_approved')>تأیید بخش</option>
                <option value="finalized" @selected(request('status')=='finalized')>قطعی</option>
                <option value="published" @selected(request('status')=='published')>اعلام عمومی</option>
            </select>
        </div>
        <div>
            <button class="btn btn-blue">اعمال</button>
            <a href="{{ route('admin.tariffs.index') }}" class="btn btn-gray">پاک کردن</a>
        </div>
    </form>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px">
        <h3 style="margin:0; border:none; padding:0">لیست مجموعه‌ها ({{ $sets->total() }})</h3>
        <a href="{{ route('admin.tariffs.create') }}" class="btn btn-green">➕ مجموعه جدید</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>روستا</th>
                <th>سال</th>
                <th>وضعیت</th>
                <th>نرخ تورم</th>
                <th>تعداد آیتم</th>
                <th>مهلت تصویب</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sets as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->village?->name }} @if(is_object($s->village?->section)) <span style="color:#90a4ae">({{ $s->village->section->name }})</span> @elseif(is_string($s->village?->section)) <span style="color:#90a4ae">({{ $s->village->section }})</span> @endif</td>
                    <td class="amount">{{ $s->jalali_year }}</td>
                    <td><span class="pill pill-{{ $s->status }}">{{ $s->statusLabel() }}</span></td>
                    <td class="amount">{{ number_format((float)$s->inflation_rate, 2) }}%</td>
                    <td class="amount">{{ $s->items()->count() }}</td>
                    <td class="amount">{{ $s->deadline_approval ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.tariffs.show', $s->id) }}" class="btn btn-blue">مشاهده</a>
                        @if(!$s->isLocked())
                            <a href="{{ route('admin.tariffs.edit', $s->id) }}" class="btn btn-yellow">ویرایش</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center; color:#607d8b; padding:24px">مجموعه‌ای یافت نشد.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top:14px">{{ $sets->links() }}</div>
</div>
@endsection
