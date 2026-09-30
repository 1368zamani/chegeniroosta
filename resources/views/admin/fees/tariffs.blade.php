@extends('admin.fees.layout')
@section('title', 'تعرفه‌ها')

@section('content')
<div class="card">
    <form method="GET" class="form-row">
        <div>
            <label>دهیاری</label>
            <select name="dehyari_id">
                <option value="">همه</option>
                @foreach($dehyaris as $d)
                    <option value="{{ $d->id }}" @selected(request('dehyari_id')==$d->id)>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label>سال</label><input type="text" name="year" value="{{ request('year') }}" class="mono"></div>
        <div style="display:flex;align-items:flex-end"><button class="btn btn-blue">فیلتر</button></div>
    </form>
    <p class="muted">⚠️ همه اعداد نمونه هستند. برای ویرایش، روی «ویرایش» بزنید و ذخیره کنید.</p>
</div>

<div class="card">
    <table>
        <thead><tr>
            <th>دهیاری</th><th>دسته</th><th>نوع عوارض</th><th>سال</th>
            <th>مبلغ پایه</th><th>نرخ واحد</th><th>ضریب</th><th>فرمول</th><th>فعال</th><th></th>
        </tr></thead>
        <tbody>
        @foreach($tariffs as $t)
            <form method="POST" action="{{ route('admin.fees.tariffs.update', $t->id) }}" id="tf-{{ $t->id }}">
                @csrf @method('PUT')
                <tr>
                    <td>{{ $t->dehyari?->name }}</td>
                    <td>{{ $t->feeType?->category?->name }}</td>
                    <td>{{ $t->feeType?->name }}</td>
                    <td class="mono">{{ $t->year }}</td>
                    <td><input type="number" name="base_amount" value="{{ $t->base_amount }}" min="0" style="width:110px" class="mono"></td>
                    <td><input type="number" name="unit_rate" value="{{ $t->unit_rate }}" min="0" style="width:110px" class="mono"></td>
                    <td><input type="number" step="0.0001" name="coefficient" value="{{ $t->coefficient }}" min="0" style="width:80px" class="mono"></td>
                    <td class="muted">{{ $t->formula_type }}</td>
                    <td><input type="checkbox" name="is_active" value="1" @checked($t->is_active)></td>
                    <td><button type="submit" class="btn btn-green">ذخیره</button></td>
                </tr>
            </form>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:14px">{{ $tariffs->links() }}</div>
</div>
@endsection
