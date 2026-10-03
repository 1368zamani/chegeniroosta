@extends('admin.fees.layout')
@section('title', 'مدیریت رمزها')

@section('content')
<div class="card">
    <h3 style="margin:0 0 6px;font-size:16px;color:#1565c0">🔑 مدیریت رمزهای کاربران</h3>
    <p class="muted" style="margin:0 0 14px">
        @if(auth()->user()->isSuperAdmin())
            شما به عنوان <b>سوپرادمین</b> می‌توانید رمز همه کاربران را تغییر دهید.
        @else
            شما می‌توانید رمز کاربران بخش <b>{{ auth()->user()->section?->name ?? '-' }}</b> و روستاهای آن را تغییر دهید.
        @endif
    </p>

    <form method="GET" class="form-row" style="background:#f5f9ff;padding:12px;border-radius:10px;margin-bottom:14px">
        <div><label>جستجو (نام/ایمیل)</label><input type="text" name="q" value="{{ request('q') }}"></div>
        <div>
            <label>نقش</label>
            <select name="role">
                <option value="">همه</option>
                <option value="super_admin" @selected(request('role')=='super_admin')>سوپرادمین</option>
                <option value="section_admin" @selected(request('role')=='section_admin')>بخشدار</option>
                <option value="fee_officer" @selected(request('role')=='fee_officer')>کارشناس مالی/عمرانی</option>
                <option value="village_admin" @selected(request('role')=='village_admin')>دهیار</option>
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
        <div style="display:flex;align-items:flex-end;gap:8px">
            <button class="btn btn-blue">🔍 فیلتر</button>
            <a href="{{ route('admin.passwords.index') }}" class="btn btn-gray">پاک کردن</a>
        </div>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>نام</th>
                <th>ایمیل</th>
                <th>نقش</th>
                <th>بخش / روستا</th>
                <th>رمز فعلی</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->name }}</td>
                <td class="mono">{{ $u->email }}</td>
                <td>
                    @php
                        $roles = [
                            'super_admin' => ['سوپرادمین', 'pill-unpaid'],
                            'section_admin' => ['بخشدار', 'pill-paid'],
                            'fee_officer' => ['کارشناس مالی', 'pill-pending_review'],
                            'village_admin' => ['دهیار', 'pill-approved'],
                        ];
                        $r = $roles[$u->role] ?? [$u->role, 'pill-pending'];
                    @endphp
                    <span class="pill {{ $r[1] }}">{{ $r[0] }}</span>
                </td>
                <td>
                    @if($u->section) بخش {{ $u->section->name }}<br>@endif
                    @if($u->village) روستای {{ $u->village->name }} @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('admin.passwords.reset', $u->id) }}" style="display:flex;gap:6px;align-items:center">
                        @csrf
                        <input type="text" name="password" placeholder="رمز جدید" required minlength="8" style="width:150px;font-family:monospace" dir="ltr" autocomplete="new-password">
                        <button type="button" class="btn btn-gray" onclick="genPass(this)" title="تولید رمز تصادفی">🎲</button>
                        <button class="btn btn-green">💾 ذخیره</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#607d8b;padding:20px">کاربری یافت نشد.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:14px">{{ $users->links() }}</div>
</div>

<script>
function genPass(btn) {
    fetch('{{ route('admin.passwords.generate') }}')
        .then(r => r.json())
        .then(data => {
            const form = btn.closest('form');
            const input = form.querySelector('input[name="password"]');
            input.value = data.password;
            input.style.background = '#d1fae5';
            setTimeout(() => input.style.background = '', 500);
        });
}
</script>
@endsection
