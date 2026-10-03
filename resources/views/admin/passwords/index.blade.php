@extends('admin.fees.layout')
@section('title', 'مدیریت کاربران و رمزها')

@section('content')
<div class="passwords-page">

{{-- ═══ هدر صفحه ═══ --}}
<div style="background: linear-gradient(135deg, #1e3a5f 0%, #0d2e4f 100%); color:#fff; padding:20px 24px; border-radius:14px; margin-bottom:16px; box-shadow:0 6px 20px rgba(13,46,79,0.25)">
    <h2 style="margin:0 0 6px; font-size:20px; color:#fff">🔑 مدیریت کاربران و رمزها</h2>
    <p style="margin:0; font-size:13px; opacity:0.9; color:#e3f2fd">
        @if(auth()->user()->isSuperAdmin())
            به عنوان <b>سوپرادمین</b> می‌توانید کاربر جدید بسازید و رمز همه کاربران را تغییر دهید.
        @else
            می‌توانید برای بخش <b>{{ auth()->user()->section?->name ?? '-' }}</b> کاربر بسازید و رمز تغییر دهید.
        @endif
    </p>
</div>

{{-- ═══ فرم ساخت کاربر جدید ═══ --}}
<div style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border:2px solid #10b981; border-radius:14px; padding:20px; margin-bottom:16px">
    <h3 style="margin:0 0 14px; font-size:16px; color:#065f46">➕ تعریف کاربر جدید</h3>

    <form method="POST" action="{{ route('admin.passwords.store') }}">
        @csrf
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px">
            <div>
                <label style="display:block; font-size:12.5px; color:#065f46; font-weight:700; margin-bottom:4px">نام و نام خانوادگی *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       style="width:100%; padding:9px 12px; border:2px solid #10b981; border-radius:8px; background:#fff; font-weight:600">
            </div>

            <div>
                <label style="display:block; font-size:12.5px; color:#065f46; font-weight:700; margin-bottom:4px">ایمیل *</label>
                <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                       style="width:100%; padding:9px 12px; border:2px solid #10b981; border-radius:8px; background:#fff; font-weight:600">
            </div>

            <div>
                <label style="display:block; font-size:12.5px; color:#065f46; font-weight:700; margin-bottom:4px">رمز عبور * (حداقل ۸ کاراکتر)</label>
                <input type="text" name="password" id="newUserPassword" value="{{ old('password') }}" required minlength="8" dir="ltr"
                       style="width:100%; padding:9px 12px; border:2px solid #10b981; border-radius:8px; background:#fff; font-family:monospace; font-weight:600">
                <button type="button" onclick="genNewPass()" style="margin-top:5px; padding:5px 12px; background:#10b981; color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:700; font-family:inherit">🎲 تولید خودکار</button>
            </div>

            <div>
                <label style="display:block; font-size:12.5px; color:#065f46; font-weight:700; margin-bottom:4px">نقش کاربر *</label>
                <select name="role" id="roleSelect" required onchange="toggleRoleFields()"
                        style="width:100%; padding:9px 12px; border:2px solid #10b981; border-radius:8px; background:#fff; font-weight:600">
                    <option value="">— انتخاب کنید —</option>
                    @if(auth()->user()->isSuperAdmin())
                        <option value="super_admin" @selected(old('role')=='super_admin')>🔴 سوپرادمین</option>
                    @endif
                    <option value="section_admin" @selected(old('role')=='section_admin')>🟢 بخشدار</option>
                    <option value="fee_officer" @selected(old('role')=='fee_officer')>💰 کارشناس مالی/عمرانی</option>
                    <option value="village_admin" @selected(old('role')=='village_admin')>🏘 دهیار</option>
                </select>
            </div>

            <div id="officerTypeField" style="display:none">
                <label style="display:block; font-size:12.5px; color:#065f46; font-weight:700; margin-bottom:4px">نوع کارشناس *</label>
                <select name="officer_type" style="width:100%; padding:9px 12px; border:2px solid #10b981; border-radius:8px; background:#fff; font-weight:600">
                    <option value="">— انتخاب کنید —</option>
                    <option value="financial" @selected(old('officer_type')=='financial')>💰 کارشناس مالی</option>
                    <option value="technical" @selected(old('officer_type')=='technical')>🏗 کارشناس فنی و عمرانی</option>
                </select>
            </div>

            <div id="villageField" style="display:none">
                <label style="display:block; font-size:12.5px; color:#065f46; font-weight:700; margin-bottom:4px">روستا *</label>
                <select name="village_id" style="width:100%; padding:9px 12px; border:2px solid #10b981; border-radius:8px; background:#fff; font-weight:600">
                    <option value="">— انتخاب کنید —</option>
                    @foreach(\App\Models\Village::orderBy('name')->get() as $v)
                        <option value="{{ $v->id }}" @selected(old('village_id')==$v->id)>{{ $v->name }}@if(is_object($v->section) && $v->section->name) ({{ $v->section->name }})@elseif(is_string($v->section) && $v->section) ({{ $v->section }})@endif</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isSuperAdmin())
                <div>
                    <label style="display:block; font-size:12.5px; color:#065f46; font-weight:700; margin-bottom:4px">بخش</label>
                    <select name="section_id" style="width:100%; padding:9px 12px; border:2px solid #10b981; border-radius:8px; background:#fff; font-weight:600">
                        <option value="">— بدون بخش —</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" @selected(old('section_id')==$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label style="display:block; font-size:12.5px; color:#065f46; font-weight:700; margin-bottom:4px">شماره تماس</label>
                <input type="text" name="phone" value="{{ old('phone') }}" dir="ltr"
                       style="width:100%; padding:9px 12px; border:2px solid #10b981; border-radius:8px; background:#fff; font-weight:600">
            </div>
        </div>

        <button type="submit" style="margin-top:14px; padding:11px 26px; background:#10b981; color:#fff; border:none; border-radius:9px; font-weight:800; font-size:14px; cursor:pointer; font-family:inherit; box-shadow:0 3px 10px rgba(16,185,129,0.3)">
            ➕ ساخت کاربر
        </button>
    </form>
</div>

{{-- ═══ فرم فیلتر ═══ --}}
<div style="background:#f5f9ff; border:1px solid #cfd8dc; border-radius:12px; padding:14px; margin-bottom:16px">
    <form method="GET" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; align-items:end">
        <div>
            <label style="display:block; font-size:12.5px; color:#37474f; font-weight:700; margin-bottom:4px">جستجو</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="نام یا ایمیل"
                   style="width:100%; padding:9px 12px; border:1px solid #cfd8dc; border-radius:8px">
        </div>
        <div>
            <label style="display:block; font-size:12.5px; color:#37474f; font-weight:700; margin-bottom:4px">نقش</label>
            <select name="role" style="width:100%; padding:9px 12px; border:1px solid #cfd8dc; border-radius:8px">
                <option value="">همه</option>
                <option value="super_admin" @selected(request('role')=='super_admin')>سوپرادمین</option>
                <option value="section_admin" @selected(request('role')=='section_admin')>بخشدار</option>
                <option value="fee_officer" @selected(request('role')=='fee_officer')>کارشناس</option>
                <option value="village_admin" @selected(request('role')=='village_admin')>دهیار</option>
            </select>
        </div>
        @if(auth()->user()->isSuperAdmin())
        <div>
            <label style="display:block; font-size:12.5px; color:#37474f; font-weight:700; margin-bottom:4px">بخش</label>
            <select name="section_id" style="width:100%; padding:9px 12px; border:1px solid #cfd8dc; border-radius:8px">
                <option value="">همه</option>
                @foreach($sections as $s)
                    <option value="{{ $s->id }}" @selected(request('section_id')==$s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div style="display:flex; gap:6px">
            <button style="padding:10px 20px; background:#1565c0; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-family:inherit">🔍 فیلتر</button>
            <a href="{{ route('admin.passwords.index') }}" style="padding:10px 20px; background:#607d8b; color:#fff; border-radius:8px; font-weight:700; text-decoration:none">پاک کردن</a>
        </div>
    </form>
</div>

{{-- ═══ لیست کاربران ═══ --}}
<div style="background:#fff; border:1px solid #e0e0e0; border-radius:12px; padding:16px">
    <h3 style="margin:0 0 14px; font-size:15px; color:#1e3a5f">لیست کاربران ({{ $users->total() }})</h3>

    <table style="width:100%; border-collapse:collapse; font-size:13.5px">
        <thead>
            <tr style="background:#f5f9ff">
                <th style="padding:10px; text-align:right; color:#1565c0; font-weight:800; border-bottom:2px solid #1565c0">#</th>
                <th style="padding:10px; text-align:right; color:#1565c0; font-weight:800; border-bottom:2px solid #1565c0">نام</th>
                <th style="padding:10px; text-align:right; color:#1565c0; font-weight:800; border-bottom:2px solid #1565c0">ایمیل</th>
                <th style="padding:10px; text-align:right; color:#1565c0; font-weight:800; border-bottom:2px solid #1565c0">نقش</th>
                <th style="padding:10px; text-align:right; color:#1565c0; font-weight:800; border-bottom:2px solid #1565c0">بخش / روستا</th>
                <th style="padding:10px; text-align:right; color:#1565c0; font-weight:800; border-bottom:2px solid #1565c0">رمز جدید</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $u)
            <tr style="border-bottom:1px solid #eceff1">
                <td style="padding:10px; color:#37474f; font-weight:700">{{ $u->id }}</td>
                <td style="padding:10px; color:#0d2e4f; font-weight:800">{{ $u->name }}</td>
                <td style="padding:10px; color:#1565c0; font-family:monospace; direction:ltr">{{ $u->email }}</td>
                <td style="padding:10px">
                    @php
                        $roles = [
                            'super_admin' => ['🔴 سوپرادمین', '#c62828'],
                            'section_admin' => ['🟢 بخشدار', '#2e7d32'],
                            'fee_officer' => ['💰 کارشناس', '#6a1b9a'],
                            'village_admin' => ['🏘 دهیار', '#e65100'],
                        ];
                        $r = $roles[$u->role] ?? [$u->role, '#607d8b'];
                    @endphp
                    <span style="display:inline-block; padding:4px 12px; background:{{ $r[1] }}; color:#fff; border-radius:20px; font-weight:700; font-size:12px">{{ $r[0] }}</span>
                </td>
                <td style="padding:10px; color:#455a64; font-weight:600">
                    @if($u->section) بخش {{ $u->section->name }}<br>@endif
                    @if($u->village) روستای {{ $u->village->name }} @endif
                </td>
                <td style="padding:10px">
                    <form method="POST" action="{{ route('admin.passwords.reset', $u->id) }}" style="display:flex; gap:5px; align-items:center">
                        @csrf
                        <input type="text" name="password" placeholder="رمز جدید" required minlength="8"
                               style="width:140px; padding:7px 10px; border:1.5px solid #1565c0; border-radius:7px; font-family:monospace; font-weight:600" dir="ltr" autocomplete="new-password">
                        <button type="button" onclick="genPass(this)" title="تولید تصادفی"
                                style="padding:7px 10px; background:#607d8b; color:#fff; border:none; border-radius:7px; cursor:pointer">🎲</button>
                        <button style="padding:7px 14px; background:#10b981; color:#fff; border:none; border-radius:7px; font-weight:700; cursor:pointer; font-family:inherit">💾</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center; padding:24px; color:#607d8b">کاربری یافت نشد.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:14px">{{ $users->links() }}</div>
</div>

</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('roleSelect').value;
    document.getElementById('officerTypeField').style.display = (role === 'fee_officer') ? 'block' : 'none';
    document.getElementById('villageField').style.display = (role === 'village_admin') ? 'block' : 'none';
}

function genPass(btn) {
    fetch('{{ route('admin.passwords.generate') }}')
        .then(r => r.json())
        .then(data => {
            const form = btn.closest('form');
            const input = form.querySelector('input[name="password"]');
            input.value = data.password;
            input.style.background = '#d1fae5';
            input.style.borderColor = '#10b981';
            setTimeout(() => {
                input.style.background = '';
                input.style.borderColor = '#1565c0';
            }, 1000);
        });
}

function genNewPass() {
    fetch('{{ route('admin.passwords.generate') }}')
        .then(r => r.json())
        .then(data => {
            const input = document.getElementById('newUserPassword');
            input.value = data.password;
            input.style.background = '#ffe0b2';
            setTimeout(() => input.style.background = '', 800);
        });
}

// اجرای اولیه
toggleRoleFields();
</script>
@endsection
