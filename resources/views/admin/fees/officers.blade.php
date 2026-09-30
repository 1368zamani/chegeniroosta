@extends('admin.fees.layout')
@section('title', 'کارشناسان مالی و عمرانی')

@section('content')
<div class="card">
    <h3 style="margin:0 0 8px;font-size:15px;color:#1565c0">🔐 کارشناسان مالی و عمرانی بخشداری</h3>
    <p class="muted" style="margin:0 0 14px">
        این کارشناسان فقط به پنل عوارض دسترسی دارند. آدرس ورود:
        <b class="mono">visian.chegeniroosta.ir/fees-login</b>
    </p>

    <form method="POST" action="{{ route('admin.fees.officers.store') }}" class="form-row" style="background:#f5f9ff;padding:14px;border-radius:10px">
        @csrf
        <div><label>نام و نام خانوادگی *</label><input type="text" name="name" required></div>
        <div><label>ایمیل *</label><input type="email" name="email" required dir="ltr"></div>
        <div><label>رمز عبور *</label><input type="text" name="password" required minlength="8" dir="ltr"></div>
        <div style="display:flex;align-items:flex-end">
            <button class="btn btn-green">➕ افزودن کارشناس</button>
        </div>
    </form>
</div>

<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;color:#1565c0">لیست کارشناسان</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>نام</th>
                <th>ایمیل</th>
                <th>تاریخ ایجاد</th>
                <th>تغییر رمز</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
        @forelse($officers as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->name }}</td>
                <td class="mono">{{ $o->email }}</td>
                <td class="mono">{{ $o->created_at?->format('Y-m-d') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.fees.officers.password', $o->id) }}" style="display:flex;gap:6px">
                        @csrf @method('PUT')
                        <input type="text" name="password" placeholder="رمز جدید" required minlength="8" style="width:140px" dir="ltr">
                        <button class="btn btn-blue">ذخیره</button>
                    </form>
                </td>
                <td>
                    <form method="POST" action="{{ route('admin.fees.officers.destroy', $o->id) }}" onsubmit="return confirm('حذف این کارشناس؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-red">حذف</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#607d8b;padding:18px">کارشناسی ثبت نشده است.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
