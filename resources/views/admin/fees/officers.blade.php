@extends('admin.fees.layout')
@section('title', 'کارشناسان مالی و فنی/عمرانی')

@section('content')
<div class="card">
    <h3 style="margin:0 0 6px;font-size:16px;color:#1565c0">🔐 کارشناسان مالی و فنی/عمرانی</h3>
    <p class="muted" style="margin:0 0 14px">
        @if(auth()->user()->isSuperAdmin())
            شما می‌توانید برای هر بخش کارشناس مالی و فنی/عمرانی تعریف کنید.
        @else
            شما می‌توانید برای بخش <b>{{ auth()->user()->section?->name ?? '-' }}</b> کارشناس مالی و فنی/عمرانی تعریف کنید.
        @endif
        <br>
        <b>آدرس ورود کارشناسان:</b>
        <span class="mono">visian.chegeniroosta.ir/fees-login</span>
    </p>

    {{-- فرم ایجاد کارشناس جدید --}}
    <form method="POST" action="{{ route('admin.fees.officers.store') }}" style="background:#f5f9ff;padding:14px;border-radius:10px;margin-bottom:14px">
        @csrf
        <div class="form-row">
            <div>
                <label>نام و نام خانوادگی *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label>ایمیل *</label>
                <input type="email" name="email" value="{{ old('email') }}" required dir="ltr">
            </div>
            <div>
                <label>رمز عبور *</label>
                <input type="text" name="password" value="{{ old('password') }}" required minlength="8" dir="ltr" placeholder="حداقل ۸ کاراکتر">
            </div>
            <div>
                <label>نوع کارشناس *</label>
                <select name="officer_type" required>
                    <option value="">— انتخاب کنید —</option>
                    <option value="financial" @selected(old('officer_type')=='financial')>💰 کارشناس مالی</option>
                    <option value="technical" @selected(old('officer_type')=='technical')>🏗 کارشناس فنی و عمرانی</option>
                </select>
            </div>
            @if(auth()->user()->isSuperAdmin())
                <div>
                    <label>بخش *</label>
                    <select name="section_id" required>
                        <option value="">— انتخاب بخش —</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" @selected(old('section_id')==$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <label>شماره تماس</label>
                <input type="text" name="phone" value="{{ old('phone') }}" dir="ltr">
            </div>
            <div>
                <label>سمت</label>
                <input type="text" name="position" value="{{ old('position') }}" placeholder="مثلاً: کارشناس ارشد">
            </div>
        </div>
        <button class="btn btn-green" style="margin-top:10px">➕ افزودن کارشناس</button>
    </form>
</div>

{{-- فیلتر --}}
<div class="card">
    <form method="GET" class="form-row">
        <div><label>جستجو</label><input type="text" name="q" value="{{ request('q') }}" placeholder="نام یا ایمیل"></div>
        <div>
            <label>نوع</label>
            <select name="type">
                <option value="">همه</option>
                <option value="financial" @selected(request('type')=='financial')>💰 مالی</option>
                <option value="technical" @selected(request('type')=='technical')>🏗 فنی و عمرانی</option>
            </select>
        </div>
        @if(auth()->user()->isSuperAdmin())
            <div>
                <label>بخش</label>
                <select name="section_id">
                    <option value="">همه</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->id }}" @selected(request('section_id')==$s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div style="display:flex;align-items:flex-end"><button class="btn btn-blue">🔍 فیلتر</button></div>
    </form>
</div>

{{-- لیست کارشناسان --}}
<div class="card">
    <h4 style="margin:0 0 12px;font-size:15px;color:#1565c0">لیست کارشناسان ({{ $officers->total() }})</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>نام</th>
                <th>ایمیل</th>
                <th>نوع</th>
                <th>بخش</th>
                <th>تماس</th>
                <th>تغییر رمز</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
        @forelse($officers as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>
                    {{ $o->name }}
                    @if($o->position)<br><span class="muted" style="font-size:11px">{{ $o->position }}</span>@endif
                </td>
                <td class="mono">{{ $o->email }}</td>
                <td>
                    @if($o->officer_type === 'financial')
                        <span class="pill pill-paid">💰 مالی</span>
                    @elseif($o->officer_type === 'technical')
                        <span class="pill pill-pending_review">🏗 فنی/عمرانی</span>
                    @else
                        <span class="pill pill-unpaid">— تعریف نشده</span>
                    @endif
                </td>
                <td>
                    @if($o->section)
                        <b>{{ $o->section->name }}</b>
                    @else
                        <span class="muted">—</span>
                    @endif
                </td>
                <td class="mono">{{ $o->phone ?: '—' }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.fees.officers.password', $o->id) }}" style="display:flex;gap:4px">
                        @csrf @method('PUT')
                        <input type="text" name="password" placeholder="رمز جدید" required minlength="8" style="width:120px" dir="ltr">
                        <button class="btn btn-blue">💾</button>
                    </form>
                </td>
                <td>
                    <form method="POST" action="{{ route('admin.fees.officers.destroy', $o->id) }}" onsubmit="return confirm('حذف این کارشناس؟')" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-red">حذف</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center;color:#607d8b;padding:20px">
                هنوز کارشناسی تعریف نشده است. از فرم بالا استفاده کنید.
            </td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:14px">{{ $officers->links() }}</div>
</div>
@endsection
